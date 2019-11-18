<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;

/**
 * Aggregate Translator Interface
 */
interface AggregateTranslatorInterface
{
	/**
	 * @param object $aggregateRoot
	 * @return int
	 */
	public function extractAggregateVersion(object $aggregateRoot): int;

	/**
	 * @param object $aggregateRoot
	 * @return string
	 */
	public function extractAggregateId(object $aggregateRoot): string;

	/**
	 * @return object reconstructed EventSourcedAggregateRoot
	 */
	public function reconstituteAggregateFromHistory(
		AggregateTypeInterface $aggregateType,
		Iterator $historyEvents
	);

	/**
	 * @param object $aggregateRoot
	 * @return \Prooph\EventStore\EventData[]
	 */
	public function extractPendingStreamEvents(object $aggregateRoot): array;

	/**
	 * @param object $aggregateRoot Aggregate Root
	 * @param Iterator $events
	 * @return void
	 */
	public function replayStreamEvents(object $aggregateRoot, Iterator $events): void;
}
