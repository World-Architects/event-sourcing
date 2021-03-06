<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Exception;

use RuntimeException;

/**
 * AggregateChangedEvent
 */
class MissingEventHandlerException extends RuntimeException
{
	/**
	 * Creates an instance with a message for the given class
	 *
	 * @param object $class Class
	 * @param string $handler Method name of the handler
	 * @return self
	 */
	public static function missingFor(object $class, string $handler): self
	{
		return new self(sprintf(
			'Missing event handler method `%s` for `%s`',
			$handler,
			get_class($class)
		));
	}
}
