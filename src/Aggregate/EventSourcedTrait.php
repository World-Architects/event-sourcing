<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Exception\MissingEventHandlerException;
use Iterator;
use RuntimeException;

/**
 * EventSourcedTrait
 */
trait EventSourcedTrait
{
	/**
	 * Current version
	 *
	 * @var int
	 */
	protected $aggregateVersion = 0;

	/**
	 * @throws RuntimeException
	 */
	public static function reconstituteFromHistory(Iterator $historyEvents): self
	{
		$instance = new static();
		$instance->replay($historyEvents);

		return $instance;
	}

	/**
	 * Replay past events
	 *
	 * @throws RuntimeException
	 */
	protected function replay(Iterator $historyEvents): void
	{
		foreach ($historyEvents as $pastEvent) {
			/**
			 * @var \Psa\EventSourcing\Aggregate\Event\AggregateChangedEvent $pastEvent
			 */
			$this->aggregateVersion = $pastEvent->aggregateVersion();
			$this->apply($pastEvent);
		}
	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 * @return string
	 */
	protected function determineEventHandlerMethodFor(AggregateChangedEventInterface $event): string
	{
		$classParts = explode('\\', get_class($event));

		return 'when' . end($classParts);
	}

	/**
	 * Apply given event
	 *
	 * @param \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 * @return void
	 */
	protected function apply(AggregateChangedEventInterface $event): void
	{
		$handlerMethod = $this->determineEventHandlerMethodFor($event);

		if (!method_exists($this, $handlerMethod)) {
			throw MissingEventHandlerException::missingFor($this, $handlerMethod);
		}

		$this->{$handlerMethod}($event);
	}

	/**
	 * Gets the aggregate version
	 *
	 * @return int
	 */
	public function aggregateVersion(): int
	{
		return $this->aggregateVersion;
	}

	/**
	 * Aggregate Id
	 *
	 * @return string
	 */
	abstract public function aggregateId(): string;
}
