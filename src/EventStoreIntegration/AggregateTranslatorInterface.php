<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;

/**
 * AggregateTranslatorInterface
 */
interface AggregateTranslatorInterface
{
	/**
	 * @param object $eventSourcedAggregateRoot
	 * @return int
	 */
	public function extractAggregateVersion($eventSourcedAggregateRoot): int;

	/**
	 * @param object $eventSourcedAggregateRoot
	 * @return string
	 */
	public function extractAggregateId($eventSourcedAggregateRoot): string;

	/**
	 * @return object reconstructed EventSourcedAggregateRoot
	 */
	public function reconstituteAggregateFromHistory(
		AggregateType $aggregateType,
		Iterator $historyEvents
	);

	/**
	 * @param object $eventSourcedAggregateRoot
	 * @return \Prooph\EventStore\EventData[]
	 */
	public function extractPendingStreamEvents($eventSourcedAggregateRoot): array;

	/**
	 * @param object $eventSourcedAggregateRoot Aggregate Root
	 * @param Iterator $events
	 * @return void
	 */
	public function replayStreamEvents($eventSourcedAggregateRoot, Iterator $events): void;
}
