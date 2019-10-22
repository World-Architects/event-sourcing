<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Prooph\EventStore\RecordedEvent;
use Psa\EventSourcing\Aggregate\AggregateType;

/**
 * EventTranslatorInterface
 */
interface EventTranslatorInterface
{
	/**
	 *
	 */
	public function toStore(string $aggregateId, AggregateType $aggregateType, array $events): array;

	/**
	 * @param \Prooph\EventStore\RecordedEvent $recordedEvent Recorded Event
	 * @return object
	 */
	public function fromStore(RecordedEvent $recordedEvent);
}
