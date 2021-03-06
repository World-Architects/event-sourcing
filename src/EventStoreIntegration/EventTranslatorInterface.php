<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Prooph\EventStore\RecordedEvent;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;

/**
 * EventTranslatorInterface
 */
interface EventTranslatorInterface
{
	/**
	 * @param string $aggregateId Aggregat Id
	 * @param \Psa\EventSourcing\Aggregate\AggregateType $aggregateType
	 * @param array $events Events
	 * @return array
	 */
	public function toStore(
		string $aggregateId,
		AggregateTypeInterface $aggregateType,
		array $events
	): array;

	/**
	 * @param \Prooph\EventStore\RecordedEvent $recordedEvent Recorded Event
	 * @return object
	 */
	public function fromStore(RecordedEvent $recordedEvent): object;

	/**
	 * @param array $typeMap Type Mapping
	 * @return self
	 */
	public function withTypeMap(array $typeMap): EventTranslatorInterface;
}
