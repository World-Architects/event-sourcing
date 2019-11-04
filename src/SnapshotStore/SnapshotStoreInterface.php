<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore;

use Psa\EventSourcing\Aggregate\AggregateRoot;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;

/**
 * SnapshotStoreInterface
 */
interface SnapshotStoreInterface
{
	/**
	 * Stores an aggregate snapshot
	 *
	 * @param \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return void
	 */
	public function store(SnapshotInterface $aggregate);

	/**
	 * Gets an aggregate snapshot if one exist
	 *
	 * @return null|\Psa\EventSourcing\SnapshotStore\SnapshotInterface
	 */
	public function get(string $aggregateId): ?SnapshotInterface;

	/**
	 * Removes an aggregate from the store
	 *
	 * @return void
	 */
	public function delete(string $aggregateId): void;
}
