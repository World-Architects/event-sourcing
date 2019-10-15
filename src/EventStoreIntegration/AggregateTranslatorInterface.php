<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;

interface AggregateTranslatorInterface
{
	/**
	 * @param object $eventSourcedAggregateRoot
	 */
	public function extractAggregateVersion($eventSourcedAggregateRoot): int;

	/**
	 * @param object $eventSourcedAggregateRoot
	 */
	public function extractAggregateId($eventSourcedAggregateRoot): string;

	/**
	 * @return object reconstructed EventSourcedAggregateRoot
	 */
	public function reconstituteAggregateFromHistory(EventSourcedAggregateInterface $aggregateType, Iterator $historyEvents);

	/**
	 * @param object $eventSourcedAggregateRoot
	 *
	 * @return \Prooph\EventStore\EventData[]
	 */
	public function extractPendingStreamEvents($eventSourcedAggregateRoot): array;

	/**
	 * @param object $eventSourcedAggregateRoot
	 * @param Iterator $events
	 */
	public function replayStreamEvents($eventSourcedAggregateRoot, Iterator $events): void;
}
