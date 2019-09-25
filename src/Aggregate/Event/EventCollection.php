<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event;

use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;

/**
 * Event Collection
 */
class EventCollection implements EventCollectionInterface
{
	/**
	 * @var int
	 */
	private $position = 0;

	/**
	 * @var array
	 */
	private $events = [];

	/**
	 * @param array $events Events
	 */
	public function __construct(array $events = [])
	{
		foreach ($events as $event) {
			$this->add($event);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int
	{
		return count($this->events);
	}

	/**
	 * @inheritDoc
	 */
	public function last(): ?AggregateChangedEventInterface
	{
		$postion = count($this->events) - 1;
		if ($postion < 0) {
			return null;
		}

		return $this->events[$postion];
	}

	/**
	 * @inheritDoc
	 */
	public function rewind(): void
	{
		$this->position = 0;
	}

	/**
	 * @inheritDoc
	 */
	public function current()
	{
		return $this->events[$this->position];
	}

	/**
	 * @inheritDoc
	 */
	public function key(): int
	{
		return $this->position;
	}

	/**
	 * @inheritDoc
	 */
	public function next(): void
	{
		++$this->position;
	}

	/**
	 * @inheritDoc
	 */
	public function valid(): bool
	{
		return isset($this->events[$this->position]);
	}

	/**
	 * Empties the collection
	 *
	 * @return void
	 */
	public function flush(): void
	{
		$this->events = [];
	}

	/**
	 * Adds a domain event
	 *
	 * @param \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 * @return void
	 */
	public function add(AggregateChangedEventInterface $event): void
	{
		$this->events[] = $event;
	}
}
