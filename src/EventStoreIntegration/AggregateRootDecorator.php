<?php

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Psa\EventSourcing\Aggregate\AggregateChangedEvent;
use Psa\EventSourcing\Aggregate\AggregateRoot;
use BadMethodCallException;
use Iterator;
use RuntimeException;

/**
 * AggregateRootDecorator
 */
class AggregateRootDecorator extends AggregateRoot
{
	/**
	 * @return self
	 */
	public static function newInstance(): self
	{
		return new static();
	}

	/**
	 * Extracts the aggregate version
	 *
	 * @param \Psa\EventSourcing\Aggregate\AggregateRoot $anAggregateRoot Aggregate Root
	 * @return int
	 */
	public function extractAggregateVersion(AggregateRoot $anAggregateRoot): int
	{
		return $anAggregateRoot->aggregateVersion;
	}

	/**
	 * @param AggregateRoot $anAggregateRoot
	 *
	 * @param \Psa\EventSourcing\Aggregate\AggregateRoot $anAggregateRoot An Aggregate Root
	 * @return array
	 */
	public function extractRecordedEvents(AggregateRoot $anAggregateRoot): array
	{
		return $anAggregateRoot->popRecordedEvents();
	}

	/**
	 * Extracts an aggregate ID
	 *
	 * @param \Psa\EventSourcing\Aggregate\AggregateRoot $anAggregateRoot Aggregate Root
	 * @return string UUID
	 */
	public function extractAggregateId(AggregateRoot $anAggregateRoot): string
	{
		return $anAggregateRoot->aggregateId();
	}

	/**
	 * @param string $aggregateRootClass Aggregate Root
	 * @param \Iterator $aggregateChangedEvents Changed events
	 * @return \Psa\EventSourcing\Aggregate\AggregateRoot
	 * @throws RuntimeException
	 */
	public function fromHistory(string $aggregateRootClass, Iterator $aggregateChangedEvents): AggregateRoot
	{
		if (!class_exists($aggregateRootClass)) {
			throw new RuntimeException(
				sprintf('Aggregate root class %s cannot be found', $aggregateRootClass)
			);
		}

		return $aggregateRootClass::reconstituteFromHistory($aggregateChangedEvents);
	}

	/**
	 * Replay stream events
	 *
	 * @param \Psa\EventSourcing\Aggregate\AggregateRoot $aggregateRoot Aggregate Root
	 * @param \Iterator $events Events
	 * @return void
	 */
	public function replayStreamEvents(AggregateRoot $aggregateRoot, Iterator $events): void
	{
		$aggregateRoot->replay($events);
	}

	/**
	 * @throws BadMethodCallException
	 * @return string
	 */
	public function aggregateId(): string
	{
		throw new BadMethodCallException('The AggregateRootDecorator does not have an id');
	}
}
