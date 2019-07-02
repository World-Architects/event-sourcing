<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\EventSourcing\Aggregate;

use Psa\EventSourcing\EventSourcing\Aggregate\EventProducerTrait;
use Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedTrait;

/**
 * Aggregate Repository Interface
 */
interface AggregateRepositoryInterface
{
	/**
	 * Gets an aggregate
	 *
	 * @param string $aggregateId Aggregate UUID
	 * @return \Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedAggregateInterface
	 */
	public function get(string $aggregateId): EventSourcedAggregateInterface;

	/**
	 * Persist an aggregate
	 *
	 * @param \Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Event Sourced Aggregate
	 */
	public function save(EventSourcedAggregateInterface $aggregate);
}
