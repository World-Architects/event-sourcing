<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventSourcing\Aggregate;

use Psa\EventSourcing\EventSourcing\Aggregate\EventProducerTrait;
use Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedTrait;

/**
 * Event Sourced Aggregate Interface
 */
interface EventSourcedAggregateInterface extends AggregateInterface
{
	/**
	 * Returns the aggregates version
	 *
	 * @return int
	 */
	public function aggregateVersion(): int;

	/**
	 * @return array
	 */
	public function popRecordedEvents(): array;
}
