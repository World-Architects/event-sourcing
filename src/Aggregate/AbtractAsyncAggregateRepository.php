<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\Aggregate;

use ArrayIterator;
use Assert\Assert;
use Prooph\EventStore\EventStoreConnection;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeMismatchException;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException;
use Psa\EventSourcing\EventStoreIntegration\AggregateRootDecorator;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslatorInterface;
use Psa\EventSourcing\EventStoreIntegration\AggregateChangedEventTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventTranslatorInterface;
use Psa\EventSourcing\SnapshotStore\SnapshotInterface;
use Psa\EventSourcing\SnapshotStore\SnapshotStoreInterface;
use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\ExpectedVersion;
use Prooph\EventStore\SliceReadStatus;
use Prooph\EventStore\StreamEventsSlice;
use RuntimeException;
use Throwable;

/**
 * Abstract Aggregate Repository
 *
 * When extending this class make sure you are setting the aggregate type
 * property with your aggregate type the repository should use.
 *
 * Alternatively, depending on your flavor and style, you can also declare the
 * AGGREGATE_TYPE constant. A recommended way of doing so is to re-use the
 * constant from your aggregate:
 *
 * const AGGREGATE_TYPE = SomeAggregate::AGGREGATE_TYPE;
 *
 * The third possibility is to implement the AggregateTypeProviderInterface.
 */
abstract class AbtractAsyncAggregateRepository implements AggregateRepositoryInterface
{
	/**
	 * @var \Prooph\EventStore\EventStoreConnection
	 */
	protected $eventStore;

	/**
	 * Snapshot Store
	 *
	 * @var null|\Psa\EventSourcing\SnapshotStore\SnapshotStoreInterface|null
	 */
	protected $snapshotStore;

	/**
	 * Aggregate Type
	 *
	 * @var \Psa\EventSourcing\Aggregate\AggregateType
	 */
	protected $aggregateType;

	/**
	 * Event Type Mapping
	 *
	 * A map of event name to event class
	 *
	 * @var array
	 */
	protected $eventTypeMapping = [];

	/**
	 * @var \Psa\EventSourcing\EventStoreIntegration\AggregateTranslatorInterface
	 */
	protected $aggregateTranslator;

	/**
	 * @var \Psa\EventSourcing\EventStoreIntegration\EventTranslatorInterface
	 */
	protected $eventTranslator;

	/**
	 * @var null|string
	 */
	protected $streamName;

	/**
	 * @var \Psa\EventSourcing\EventStoreIntegration\AggregateRootDecorator
	 */
	protected $aggregateDecorator;

	/**
	 * @var int
	 */
	protected $eventsPerSlice = 64;

	/**
	 * Constructor
	 *
	 * @param \Prooph\EventStore\EventStoreConnection $eventStore Event Store Connection
	 */
	public function __construct(
		\Prooph\EventStore\Async\EventStoreConnection $eventStore,
		AggregateTranslatorInterface $aggregateTranslator,
		EventTranslatorInterface $eventTranslator,
		?SnapshotStoreInterface $snapshotStore = null
	) {
		$this->eventStore = $eventStore;
		$this->aggregateTranslator = $aggregateTranslator;
		$this->eventTranslator = $eventTranslator;
		$this->snapshotStore = $snapshotStore;
		$this->aggregateDecorator = AggregateRootDecorator::newInstance();
		$this->determineAggregateType();
	}

	/**
	 * Determines and checks the aggregate type for this repository
	 *
	 * @return void
	 */
	protected function determineAggregateType(): void
	{
		if (defined('static::AGGREGATE_TYPE')) {
			$this->aggregateType = static::AGGREGATE_TYPE;
		}

		if (is_string($this->aggregateType)) {
			$this->aggregateType = AggregateType::fromString($this->aggregateType);
			return;
		}

		if ($this instanceof AggregateTypeProviderInterface) {
			$this->aggregateType = $this->aggregateType();
			return;
		}

		if (is_array($this->aggregateType)) {
			$this->aggregateType = AggregateType::fromMapping($this->aggregateType);
			return;
		}

		if (!$this->aggregateType instanceof AggregateType) {
			throw new RuntimeException(sprintf(
				'%s::$aggregateType is a not string or %s. %s given.',
				self::class,
				AggregateType::class,
				is_object($this->aggregateType)
					? get_class($this->aggregateType)
					: gettype($this->aggregateType)
			));
		}
	}

	/**
	 * Deletes an aggregate
	 *
	 * @param string $aggregateId Aggregate UUID
	 */
	public function delete(string $aggregateId, $hardDelete = false)
	{
		Assert::that($aggregateId)->uuid($aggregateId);

		if ($this->snapshotStore) {
			$this->snapshotStore->delete($aggregateId);
		}

		$this->eventStore->deleteStream($aggregateId, ExpectedVersion::ANY, $hardDelete);
	}

	/**
	 * Load an aggregate from the snapshot store
	 *
	 * - Checks if a snapshot store is present for this instance of the aggregate repo
	 * - Checks if a snapshot was found for the given aggregate id
	 * - Checks if the snapshots aggregate type matches the repositories type
	 * - Fetches and replays the events after the aggregate version of restored from the snapshot
	 *
	 * @param string $aggregateId Aggregate Id
	 * @return null|\Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface
	 */
	protected function loadFromSnapshotStore(string $aggregateId): EventSourcedAggregateInterface
	{
		Assert::that($aggregateId)->uuid($aggregateId);

		if (!$this->snapshotStore) {
			return null;
		}

		$snapshot = $this->snapshotStore->get($aggregateId);

		if ($snapshot === null) {
			return null;
		}

		$this->snapshotMatchesAggregateType($snapshot);

		$lastVersion = $snapshot->lastVersion();
		$aggregateRoot = $snapshot->aggregateRoot();

		$events = $this->getEventsFromPosition(
			$snapshot->aggregateId(),
			$snapshot->lastVersion() + 1
		);

		$this->aggregateDecorator->replayStreamEvents($aggregateRoot, $events);

		return $aggregateRoot;
	}

	/**
	 * Checks if the snapshot matches the repositories aggregate type
	 *
	 * @param \Psa\EventSourcing\SnapshotStore\SnapshotInterface $snapshot Snapshot
	 * @return void
	 */
	protected function snapshotMatchesAggregateType(SnapshotInterface $snapshot): void
	{
		if ($snapshot->aggregateType() !== $this->aggregateType) {
			throw AggregateTypeMismatchException::mismatch(
				$snapshot->aggregateType(),
				$this->aggregateType->toString()
			);
		}
	}

	/**
	 * Creates a snapshot of the aggregate
	 *
	 * @return void
	 */
	public function createSnapshot(SnapshotInterface $snapshot): void
	{
		$this->snapshotStore->store($snapshot);
	}

	/**
	 * Gets an aggregate
	 *
	 * @param string $aggregateId Aggregate UUID
	 * @return object
	 */
	public function getAggregate(string $aggregateId): object
	{
		Assert::that($aggregateId)->uuid($aggregateId);

		if ($this->snapshotStore) {
			$result = $this->loadFromSnapshotStore($aggregateId);
			if ($result !== null) {
				return $result;
			}
		}

		return $this->aggregateTranslator->reconstituteAggregateFromHistory(
			$this->aggregateType,
			$this->getEventsFromPosition($aggregateId, 0)
		);
	}

	/**
	 * Get events from position
	 *
	 * @param string $aggregateId Aggregate Id
	 * @param int $position Position
	 * @return \Iterator
	 */
	protected function getEventsFromPosition(string $aggregateId, int $position): \Iterator
	{
		Assert::that($aggregateId)->uuid($aggregateId);

		$events = yield new ArrayIterator([]);
		$eventTranslator = $this->eventTranslator->withTypeMap($this->eventTypeMapping);
		$streamName = $this->determineStreamName($aggregateId);

		$promise = $this->eventStore->readStreamEventsForwardAsync(
			$streamName,
			$position,
			$this->eventsPerSlice
		);

		/*
		if ($slice === null) {
			return $events;
		}

		if (!$slice->status()->equals(SliceReadStatus::success())) {
			// Handle error
			var_dump($slice);
		}

		if ($slice->isEndOfStream()) {
			foreach ($slice->events() as $resolvedEvent) {
				$events[] = $eventTranslator->fromStore($resolvedEvent->event());
			}

			return $events;
		}

		while (!$slice->isEndOfStream()) {
			$slice = $this->eventStore->readStreamEventsForwardAsync(
				$streamName,
				$slice->lastEventNumber() + 1,
				$this->eventsPerSlice
			);

			foreach ($slice->events() as $resolvedEvent) {
				$events[] = $eventTranslator->fromStore($resolvedEvent->event());
			}
		}

		return $events;
		*/
		$promise->onResolve(function(?Throwable $reason, $slice) use ($events, $eventTranslator) {
			if ($reason !== null) {
				var_dump($reason);
				die();
			}

			if (!$slice->status()->equals(SliceReadStatus::success())) {
				// Handle error
				var_dump($slice);
			}

			if ($slice->isEndOfStream()) {
				foreach ($slice->events() as $resolvedEvent) {
					$events[] = $eventTranslator->fromStore($resolvedEvent->event());
				}

				return $events;
			}

			while (!$slice->isEndOfStream()) {
				$slice = $this->eventStore->readStreamEventsForwardAsync(
					$streamName,
					$slice->lastEventNumber() + 1,
					$this->eventsPerSlice
				);

				foreach ($slice->events() as $resolvedEvent) {
					$events[] = $eventTranslator->fromStore($resolvedEvent->event());
				}
			}
		});

		return $events;
	}

	/**
	 * @param object $aggregate Aggregate
	 */
	public function saveAggregate(object $aggregate): void
	{
		$aggregateId = $this->aggregateTranslator->extractAggregateId($aggregate);
		$events = $this->aggregateTranslator->extractPendingStreamEvents($aggregate);
		$events = $this->eventTranslator->toStore($aggregateId, $this->aggregateType, $events);
		$streamName = $this->determineStreamName($aggregateId);
		$this->assertAggregateType($aggregate);

		$this->eventStore->appendToStreamAsync(
			$streamName,
			ExpectedVersion::ANY,
			$events
		);
	}

	/**
	 * @param object $eventSourcedAggregateRoot
	 */
	protected function assertAggregateType(object $eventSourcedAggregateRoot)
	{
		$this->aggregateType->assert($eventSourcedAggregateRoot);
	}

	/**
	 * Default stream name generation.
	 *
	 * Override this method in an extending repository to provide a custom name
	 */
	protected function determineStreamName(string $aggregateId): string
	{
		if ($this->streamName === null) {
			$prefix = (string)$this->aggregateType;
			$prefix = str_replace('\\', '', $prefix);
		} else {
			$prefix = $this->streamName;
		}

		return $prefix . '-' . $aggregateId;
	}
}
