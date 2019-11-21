<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

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
	 * @param string $eventType Aggregate Type
	 * @param string $otherEventType Other Aggregate Type
	 * @return static
	 */
	public static function typeMismatch(string $eventType, string $otherEventType)
	{
		return new self(sprintf(
			'Event types must be equal: `%s` does not match `%s`',
			$eventType,
			$otherEventType
		));
	}

	/**
	 * @param string $class Class
	 * @param int $eventNumber Event Number
	 * @param string $eventId Event Id
	 * @return self
	 */
	public static function mappingFailed(
		string $class,
		int $eventNumber,
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
