<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\Aggregate;

use Psa\EventSourcing\Aggregate\EventProducerTrait;
use Psa\EventSourcing\Aggregate\EventSourcedTrait;

/**
 * Aggregate Repository Interface
 */
interface AggregateRepositoryInterface
{
	/**
	 * Gets an aggregate
	 *
	 * @param string $aggregateId Aggregate UUID
	 * @return \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface
	 */
	public function getAggregate(string $aggregateId): EventSourcedAggregateInterface;

	/**
	 * Persist an aggregate
	 *
	 * @param object $aggregate Event Sourced Aggregate
	 */
	public function saveAggregate(object $aggregate);
}
