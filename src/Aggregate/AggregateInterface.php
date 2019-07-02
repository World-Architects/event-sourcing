<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\EventSourcing\Aggregate;

use Psa\EventSourcing\EventSourcing\Aggregate\EventProducerTrait;
use Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedTrait;

/**
 * Aggregate Interface
 */
interface AggregateInterface
{
	/**
	 * Returns the aggregates UUID as string
	 *
	 * @return string
	 */
	public function aggregateId(): string;
}
