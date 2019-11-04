<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore;

use DateTimeImmutable;
use Psa\EventSourcing\Aggregate\AggregateTypeProviderInterface;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;

/**
 * Snapshot Factory
 */
class SnapshotFactory
{
	/**
	 * Creates a snapshop from an aggregat implementing EventSourcedAggregateInterface
	 *
	 * @param \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return \Psa\EventSourcing\SnapshotStore\SnapshotInterface
	 */
	public static function fromEventSourcedAggregate(EventSourcedAggregateInterface $aggregate): SnapshotInterface
	{
		$aggregateType = get_class($aggregate);
		if ($aggregate instanceof AggregateTypeProviderInterface) {
			$aggregateType = $aggregate->aggregateType();
		}

		return new Snapshot(
			$aggregateType,
			$aggregate->aggregateId(),
			$aggregate,
			$aggregate->aggregateVersion(),
			new DateTimeImmutable()
		);
	}
}
