<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\EventSourcing\SnapshotStore;

use Psa\EventSourcing\EventSourcing\Aggregate\AggregateRoot;
use Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedAggregateInterface;

/**
 * SnapshotStoreInterface
 */
interface SnapshotStoreInterface
{
	/**
	 * Stores an aggregate snapshot
	 *
	 * @param \Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return void
	 */
	public function store(EventSourcedAggregateInterface $aggregate);

	/**
	 * Gets an aggregate snapshot if one exist
	 *
	 * @return mixed
	 */
	public function get(string $aggregateId): ?SnapshotInterface;
}
