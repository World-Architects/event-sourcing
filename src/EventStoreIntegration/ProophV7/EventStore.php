<?php
namespace Psa\EventSourcing\EventStoreIntegration\ProophV7;

use Psa\EventSourcing\Aggregate\Event\EventCollectionInterface;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Psa\EventSourcing\Aggregate\Exception\EventTypeException;

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

	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return void
	 */
	public function saveAggregate(EventSourcedAggregateInterface $aggregate): void
	{

	}
}
