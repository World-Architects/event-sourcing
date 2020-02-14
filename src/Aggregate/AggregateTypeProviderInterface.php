<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

interface AggregateTypeProviderInterface
{
	public function aggregateType(): AggregateType;
}
