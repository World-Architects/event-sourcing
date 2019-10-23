<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event\Exception;

use Exception;

/**
 * EventTypeException
 */
class EventTypeException extends EventException
{
	/**
	 * @param mixed $type Type
	 * @return static
	 */
	public static function notAnObject($type)
	{
		return new self(sprintf(
			'Event type must be an object but type of %s given',
			gettype($type)
		));
	}

	/**
	 * @param string $aggregateType Aggregate Type
	 * @param string $otherAggregateType Other Aggregate Type
	 * @return static
	 */
	public static function typeMismatch(string $aggregateType, string $otherAggregateType)
	{
		return new self(sprintf(
			'Event types must be equal. %s != %s',
			$aggregateType,
			$otherAggregateType
		));
	}

	/**
	 * @param string $class Class
	 * @param string $eventNumber Event Number
	 * @param string $eventId Event Id
	 * @return self
	 */
	public static function mappingFailed(
		string $class,
		string $eventNumber,
		string $eventId
	) {
		return new self(sprintf(
			'Mapping of %s failed. Version %s ID %s',
			$class,
			$eventNumber,
			$eventId
		));
	}
}
