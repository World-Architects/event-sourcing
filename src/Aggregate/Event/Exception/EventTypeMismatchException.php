<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event\Exception;

use RuntimeException;

/**
 * AggregateTypeMismatchException
 */
class EventTypeMismatchException extends RuntimeException
{
	/**
	 * @param string $type1 Type
	 * @param string $type2 Other type
	 * @return self
	 */
	public static function mismatch(string $type1, string $type2): self
	{
		return new self(sprintf(
			'Event type mismatch: `%s` doesn`t match the repositories type `%s`',
			$type1,
			$type2
		));
	}
}
