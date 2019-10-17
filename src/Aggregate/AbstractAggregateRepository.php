<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\Aggregate;

use Assert\Assert;
use Prooph\EventStore\EventStoreConnection;
use Psa\EventSourcing\Aggregate\Event\EventCollection;
use Psa\EventSourcing\Aggregate\Event\EventCollectionInterface;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeMismatchException;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException;
use Psa\EventSourcing\EventStoreIntegration\AggregateRootDecorator;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslatorInterface;
use Psa\EventSourcing\SnapshotStore\SnapshotInterface;
use Psa\EventSourcing\SnapshotStore\SnapshotStoreInterface;
use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\ExpectedVersion;
use Prooph\EventStore\SliceReadStatus;
use Prooph\EventStore\StreamEventsSlice;
use Psa\Foundation\CorrelationId;
use RuntimeException;

/**
 * Aggregate Repository
 *
 * When extending this class make sure you are setting the aggregate type
 * property with your aggregate type the repository should use.
 */
abstract class AbstractAggregateRepository implements AggregateRepositoryInterface
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
	 * @var null|string
	 */
	protected $streamName;

	/**
	 * @var \Psa\EventSourcing\EventStoreIntegration\AggregateRootDecorator
	 */
	protected $aggregateDecorator;

	/**
	 * Constructor
	 *
	 * @param \Prooph\EventStore\EventStoreConnection $eventStore Event Store Connection
	 */
	public function __construct(
		EventStoreConnection $eventStore,
		AggregateTranslatorInterface $aggregateTranslator,
		AggregateType $aggregateType,
		?SnapshotStoreInterface $snapshotStore = null
	) {
		$this->eventStore = $eventStore;
		$this->aggregateTranslator = $aggregateTranslator;
		$this->snapshotStore = $snapshotStore;
		$this->aggregateDecorator = AggregateRootDecorator::newInstance();
		$this->aggregateType = $aggregateType;
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

		$events = $this->getEventsFromPosition($snapshot->aggregateId(), $snapshot->lastVersion() + 1);

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
	public function createSnapshot(EventSourcedAggregateInterface $aggregate): void
	{
		$this->snapshotStore->store($aggregate);
	}

	/**
	 * Gets an aggregate
	 *
	 * @param string $aggregateId Aggregate UUID
	 * @return \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface
	 */
	public function getAggregate(string $aggregateId): EventSourcedAggregateInterface
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
	 * @return \Psa\EventSourcing\Aggregate\Event\EventCollectionInterface
	 */
	protected function buildEventCollection(): EventCollectionInterface
	{
		return new EventCollection();
	}

	/**
	 * Get events from position
	 *
	 * @param string $aggregateId Aggregate Id
	 * @param int $position Position
	 * @return \Psa\EventSourcing\Aggregate\Event\EventCollectionInterface
	 */
	protected function getEventsFromPosition(string $aggregateId, int $position)
	{
		Assert::that($aggregateId)->uuid($aggregateId);

		$streamName = $this->determineStreamName($aggregateId);

		$eventsSlice = $this->eventStore->readStreamEventsForward($streamName, $position, 1024);
		$eventCollection = $this->buildEventCollection();

		if ($eventsSlice->isEndOfStream()) {
			$this->convertEvents($eventsSlice, $eventCollection);

			return $eventCollection;
		}

		while (!$eventsSlice->isEndOfStream()) {
			$this->convertEvents($eventsSlice, $eventCollection);
			$eventsSlice = $this->eventStore->readStreamEventsForward($aggregateId, $eventsSlice->lastEventNumber() + 1, 1024);
		}

		return $eventCollection;
	}

	/**
	 * @todo Refactor this? I have the feeling this can be done a lot better
	 *
	 * @param \Prooph\EventStore\StreamEventsSlice $eventsSlice Event Slice
	 * @param \Psa\EventSourcing\Aggregate\Event\EventCollectionInterface $eventCollection Event Collection
	 * @return void
	 */
	protected function convertEvents(
		StreamEventsSlice $eventsSlice,
		EventCollectionInterface $eventCollection
	) {
		foreach ($eventsSlice->events() as $event) {
			/**
			 * @var \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event
			 */
			$eventType = $event->event()->eventType();
			$metaData = json_decode($event->event()->metadata(), true);
			$payload = $event->event()->data();

			if (isset($this->eventTypeMapping[$eventType])) {
				$eventClass = $this->eventTypeMapping[$eventType];
			} else {
				$eventClass = $eventType;
			}

			if (!class_exists($eventClass)) {
				throw EventTypeException::mappingFailed(
					$eventClass,
					$event->event()->eventNumber(),
					$event->event()->eventId()->toString()
				);
			}

			if ($event->event()->isJson()) {
				$payload = json_decode($payload, true);
			}

			/**
			 * @var \Psa\EventSourcing\Aggregate\AggregateChangedEvent $event
			 */
			$event = $eventClass::occur(
				$metaData['_aggregate_id'],
				$payload
			);
			$event = $event->withMetadata($metaData);
			//$event = $event->withVersion($metaData['_aggregate_version']);

			$eventCollection->add($event);
		}
	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return void
	 */
	public function saveAggregate(EventSourcedAggregateInterface $aggregate): void
	{
		$aggregateId = $this->aggregateTranslator->extractAggregateId($aggregate);
		$events = $this->aggregateTranslator->extractPendingStreamEvents($aggregate);
		$streamName = $this->determineStreamName($aggregateId);
		$this->assertAggregateType($aggregate);

		$storeEvents = [];
		foreach ($events as $event) {
			/**
			 * @var \Psa\EventSourcing\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event
			 */
			$eventType = EventType::fromEvent($event);

			$event = $this->enrichEventMetadata($event, $aggregateId, $this->aggregateType->toString());

			$storeEvents[] = new EventData(
				EventId::generate(),
				$eventType->toString(),
				true,
				json_encode($event->payload()),
				json_encode($event->metadata())
			);
		}

		$this->eventStore->appendToStream(
			$streamName,
			ExpectedVersion::ANY,
			$storeEvents
		);
	}

	/**
	 * @param object $eventSourcedAggregateRoot
	 */
	protected function assertAggregateType($eventSourcedAggregateRoot)
	{
		$this->aggregateType->assert($eventSourcedAggregateRoot);
	}

	/**
	 *
	 */
	public function enrichEventMetadata(
		AggregateChangedEventInterface $event,
		string $aggregateId,
		string $aggregateType
	): AggregateChangedEventInterface {
		return $event
			->withAddedMetadata('_aggregate_id', $aggregateId)
			->withAddedMetadata('_aggregate_type', $aggregateType)
			->withAddedMetadata('_aggregate_version', $event->aggregateVersion());
	}

	/**
	 * Default stream name generation.
	 *
	 * Override this method in an extending repository to provide a custom name
	 */
	protected function determineStreamName(string $aggregateId): string
	{
		if ($this->streamName === null) {
			$prefix = $this->aggregateType->toString();
		} else {
			$prefix = $this->streamName;
		}

		return $prefix . '-' . $aggregateId;
	}
}
