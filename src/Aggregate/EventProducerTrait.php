<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventSourcing\Aggregate;

use Psa\EventSourcing\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;

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
	protected $version = 0;

	/**
	 * List of events that are not committed to the EventStore
	 *
	 * @var AggregateChangedEvent[]
	 */
	protected $recordedEvents = [];

	/**
	 * Get pending events and reset stack
	 *
	 * @return AggregateChangedEvent[]
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
	 * @param \Psa\EventSourcing\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 */
	protected function recordThat(AggregateChangedEventInterface $event): void
	{
		$this->version += 1;

		$this->recordedEvents[] = $event->withVersion($this->version);

		$this->apply($event);
	}

	/**
	 * @inheritDoc
	 */
	abstract public function aggregateId(): string;

	/**
	 * Apply given event
	 */
	abstract protected function apply(AggregateChangedEventInterface $event): void;
}
