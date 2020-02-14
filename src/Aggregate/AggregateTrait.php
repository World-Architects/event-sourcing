<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use Iterator;

/**
 * Aggregate Trait
 *
 * This trait features a dependency minimalistic implementation for an event
 * sourced aggregate.
 */
trait AggregateTrait
{
	/**
	 * @var array
	 */
	protected $recordedEvents = [];

	/**
	 * @var int
	 */
	protected $aggregateVersion = 0;

	/*
	 * @var string|object
	 */
	protected $aggregateId;

	/**
	 * @inheritDoc
	 */
	public function aggregateId(): string
	{
		return (string)$this->aggregateId;
	}

	/**
	 * @param object $event Event
	 * @return void
	 */
	protected function recordThat(object $event): void
	{
		$this->aggregateVersion++;
		$this->recordedEvents[] = $event;
		$this->applyEvent($event);
	}

	/**
	 * @param \Iterator $events Events
	 * @return self
	 */
	public static function reconstituteFromHistory(Iterator $events): self
	{
		$instance = new static();
		$instance->replayEvents($events);

		return $instance;
	}

	/**
	 * Replays a list of events on this aggregate
	 *
	 * It is protected for the reason of not allowing the "public" to access this
	 * method from the outside. Our event store implementation will take care
	 * of that by using reflections to call the method anyway when we need to
	 * add events.
	 *
	 * @param \Iterator $events Events
	 * @return void
	 */
	protected function replayEvents(Iterator $events): void
	{
		foreach ($events as $event) {
			$this->aggregateVersion++;
			$this->applyEvent($event);
		}
	}

	/**
	 * @param object $event Event Object
	 * @return void
	 */
	protected function applyEvent(object $event): void
	{
		$classParts = explode('\\', get_class($event));
		$method = 'when' . end($classParts);

		if (method_exists($this, $method)) {
			$this->{$method}($event);
		}
	}
}
