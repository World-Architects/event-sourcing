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
 * Aggregate Root
 */
abstract class AggregateRoot implements EventSourcedAggregateInterface
{
	use EventProducerTrait;
	use EventSourcedTrait;

	/**
	 * We do not allow public access to __construct, this way we make sure that
	 * an aggregate root can only be constructed by static factories
	 */
	protected function __construct()
	{
	}
}
