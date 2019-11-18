<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use Psa\EventSourcing\Aggregate\EventProducerTrait;
use Psa\EventSourcing\Aggregate\EventSourcedTrait;

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
