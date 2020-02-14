<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;

/**
 * Event Producer Trait
 */
trait EventProducerTrait
{
	/**
	 * Current version
	 *
	 * @var int
	 */
	protected $aggregateVersion = 0;

	/**
	 * List of events that are not committed to the EventStore
	 *
	 * @var \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface[]
	 */
	protected $recordedEvents = [];

	/**
	 * Get pending events and reset stack
	 *
	 * @return \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface[]
	 */
	public function popRecordedEvents(): array
	{
		$pendingEvents = $this->recordedEvents;

		$this->recordedEvents = [];

		return $pendingEvents;
	}

	/**
	 * Record an aggregate changed event
	 *
	 * @param \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 * @return void
	 */
	protected function recordThat(AggregateChangedEventInterface $event): void
	{
		$this->aggregateVersion += 1;

		$this->recordedEvents[] = $event->withAggregateVersion($this->aggregateVersion);

		$this->apply($event);
	}

	/**
	 * @inheritDoc
	 */
	abstract public function aggregateId(): string;

	/**
	 * Apply given event
	 *
	 * @param \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 * @return void
	 */
	abstract protected function apply(AggregateChangedEventInterface $event): void;
}
