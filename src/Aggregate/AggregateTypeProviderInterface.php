<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

interface AggregateTypeProviderInterface
{
	public function aggregateType(): AggregateType;
}
