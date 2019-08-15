<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Exception;

use RuntimeException;

/**
 * EventTypeException
 */
class EventTypeException extends RuntimeException
{
	/**
	 * Creates an instance with a message for the given class
	 *
	 * @return self
	 */
	public static function mappingFailed(string $eventType, int $eventNumber, string $eventId): self
	{
		return new self(sprintf(
			'Mapping or class for event type `%s` with event number %d and ID `%s` not found',
			$eventType,
			$eventNumber,
			$eventId
		));
	}
}
