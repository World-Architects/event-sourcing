<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event;

use InvalidArgumentException;
use Psa\EventSourcing\Aggregate\AggregateTypeProviderInterface;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeException;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException;

/**
 * Event Type
 */
class EventType
{
	/**
	 * @var string|null
	 */
	protected $eventType;

	/**
	 * @var array
	 */
	protected $mapping = [];

	/**
	 * @var string
	 */
	protected $eventTypeConstant = 'EVENT_TYPE';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	private function __construct()
	{
	}

	/**
	 * Use this factory when aggregate type should be detected based on given aggregate root
	 *
	 * @param object $event Event object
	 * @throws \Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException
	 */
	public static function fromEvent(object $event): EventType
	{
		// Check if the aggregate implements the type provider
		if ($event instanceof EventTypeProviderInterface) {
			return $event->eventType();
		}

		$self = new static();
		$eventClass = get_class($event);
		$typeConstant = $eventClass . '::' . $self->eventTypeConstant;

		// Check if the aggregate has the type defined as constant
		if (defined($typeConstant)) {
			$self->eventType = constant($typeConstant);

			return $self;
		}

		// Fall back to the FQCN as type
		$self->eventType = $eventClass;

		return $self;
	}

	/**
	 * Use this factory when aggregate type equals to aggregate root class
	 * The factory makes sure that the aggregate root class exists.
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function fromEventClass(string $eventRootClass): EventType
	{
		if (!class_exists($eventRootClass)) {
			throw new InvalidArgumentException(sprintf('Event class %s can not be found', $eventRootClass));
		}

		$self = new static();
		$self->eventType = $eventRootClass;

		return $self;
	}

	/**
	 * Use this factory when the aggregate type is not equal to the aggregate root class
	 *
	 * @param string $eventTypeString Aggregate Type String
	 * @throws \InvalidArgumentException
	 */
	public static function fromString(string $eventTypeString): EventType
	{
		if (empty($eventTypeString)) {
			throw new InvalidArgumentException('Event Type must be a non empty string');
		}

		$self = new static();
		$self->eventType = $eventTypeString;

		return $self;
	}

	/**
	 * @param array $mapping Mapping
	 * @return static
	 */
	public static function fromMapping(array $mapping): EventType
	{
		$self = new static();
		$self->mapping = $mapping;

		return $self;
	}

	/**
	 * @return null|string
	 */
	public function mappedClass(): ?string
	{
		return empty($this->mapping) ? null : current($this->mapping);
	}

	/**
	 * @return string
	 */
	public function toString(): string
	{
		return empty($this->mapping) ? (string)$this->eventType : (string)key($this->mapping);
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->toString();
	}

	/**
	 * @param object $event Event object
	 * @throws \Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException
	 */
	public function assert(object $event): void
	{
		$otherEvent = self::fromEvent($event);

		if (!$this->equals($otherEvent)) {
			throw EventTypeException::typeMismatch(
				$this->toString(),
				$otherEvent->toString()
			);
		}
	}

	/**
	 * Checks if two instances of this class are equal
	 *
	 * @return bool
	 */
	public function equals(EventType $other): bool
	{
		if (!$eventTypeString = $this->mappedClass()) {
			$eventTypeString = $this->toString();
		}

		return $eventTypeString === $other->toString()
			|| $eventTypeString === $other->mappedClass();
	}
}
