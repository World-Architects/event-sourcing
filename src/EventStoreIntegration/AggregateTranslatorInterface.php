<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Psa\EventSourcing\Aggregate\AggregateType;
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
	public function extractAggregateVersion($aggregateRoot): int;

	/**
	 * @param object $aggregateRoot
	 * @return string
	 */
	public function extractAggregateId($aggregateRoot): string;

	/**
	 * @return object reconstructed EventSourcedAggregateRoot
	 */
	public function reconstituteAggregateFromHistory(
		AggregateType $aggregateType,
		Iterator $historyEvents
	);

	/**
	 * @param object $aggregateRoot
	 * @return \Prooph\EventStore\EventData[]
	 */
	public function extractPendingStreamEvents($aggregateRoot): array;

	/**
	 * @param object $aggregateRoot Aggregate Root
	 * @param Iterator $events
	 * @return void
	 */
	public function replayStreamEvents($aggregateRoot, Iterator $events): void;
}
