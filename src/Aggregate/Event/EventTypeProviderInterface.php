<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event;

/**
 * Event Type Provider Interface
 */
interface EventTypeProviderInterface
{
	/**
	 * @return \Psa\EventSourcing\Aggregate\Event\EventType
	 */
	public function eventType(): EventType;
}
