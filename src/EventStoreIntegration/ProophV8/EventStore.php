<?php
namespace Psa\EventSourcing\EventStoreIntegration\ProophV8;

use Psa\EventSourcing\Aggregate\Event\EventCollectionInterface;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Psa\EventSourcing\Aggregate\Exception\EventTypeException;
use Psa\EventSourcing\EventStoreIntegration\AggregateRootDecorator;
use Psa\EventSourcing\SnapshotStore\SnapshotStoreInterface;

class EventStore
{
	/**
	 * @var \Prooph\EventStore\EventStoreConnection
	 */
	protected $eventStore;

	/**
	 * Snapshot Store
	 *
	 * @var \Psa\EventSourcing\SnapshotStore\SnapshotStoreInterface|null
	 */
	protected $snapshotStore;

	/**
	 * Aggregate Type
	 *
	 * @var string
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
	 * Constructor
	 *
	 * @param \Prooph\EventStore\EventStoreConnection $eventStore Event Store Connection
	 */
	public function __construct(
		EventStoreConnection $eventStore
	) {
		$this->eventStore = $eventStore;
	}

	/**
	 * Get events from position
	 */
	protected function getEventsFromPosition(string $aggregateId, int $position)
	{
		$eventsSlice = $this->eventStore->readStreamEventsForward($aggregateId, $position, 50);
		$eventCollection = $this->buildEventCollection();

		if ($eventsSlice->isEndOfStream()) {
			$this->convertEvents($eventsSlice, $eventCollection);

			return $eventCollection;
		}

		while (!$eventsSlice->isEndOfStream()) {
			$this->convertEvents($eventsSlice, $eventCollection);
			$eventsSlice = $this->eventStore->readStreamEventsForward($aggregateId, $eventsSlice->lastEventNumber() + 1, 5);
		}

		return $eventCollection;
	}

	/**
	 * @todo Refactor this? I have the feeling this can be done a lot better
	 * @param \Prooph\EventStore\StreamEventsSlice $eventsSlice Event Slice
	 * @param \Psa\EventSourcing\Aggregate\Event\EventCollectionInterface Event Collection
	 * @return void
	 */
	protected function convertEvents(
		StreamEventsSlice $eventsSlice,
		EventCollectionInterface $eventCollection
	) {
		foreach ($eventsSlice->events() as $event) {
			/**
			 * @var $event \Prooph\EventStore\Internal\ResolvedEvent
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
			 * @var $event \Psa\EventSourcing\Aggregate\AggregateChangedEvent
			 */
			$event = $eventClass::occur(
				$metaData['_aggregate_id'],
				$payload
			);
			$event = $event->withMetadata($metaData);
			$event = $event->withVersion($metaData['_aggregate_version']);

			$eventCollection->add($event);
		}
	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return void
	 */
	public function saveAggregate(EventSourcedAggregateInterface $aggregate): void
	{
		$aggregateId = $aggregate->aggregateId();
		$aggregateType = get_class($aggregate);
		$events = $aggregate->popRecordedEvents();

		$storeEvents = [];
		foreach ($events as $event) {
			$eventClass = get_class($event);
			$eventTypeContstant = $eventClass . '::EVENT_TYPE';
			$eventVersionContstant = $eventClass . '::EVENT_VERSION';

			if (defined($eventTypeContstant)) {
				$eventType = $event::EVENT_TYPE;
			} else {
				throw new RuntimeException(sprintf(
					'Event Class Constant %s is missing',
					$eventTypeContstant
				));
			}

			$eventVersion = 1;
			if (defined($eventVersionContstant)) {
				$eventVersion = $event::EVENT_VERSION;
			}

			$storeEvents[] = new EventData(
				EventId::generate(),
				$eventType,
				true,
				json_encode($event->payload()),
				json_encode([
					'_event_version' => $eventVersion,
					'_aggregate_id' => $aggregateId,
					'_aggregate_type' => $aggregateType,
					'_aggregate_version' => $event->aggregateVersion()
				])
			);
		}

		$this->eventStore->appendToStream(
			'Account-' . $aggregateId,
			ExpectedVersion::ANY,
			$storeEvents
		);
	}
}
