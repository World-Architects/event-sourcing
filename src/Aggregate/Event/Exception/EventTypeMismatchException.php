<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event\Exception;

use RuntimeException;

/**
 * AggregateTypeMismatchException
 */
class EventTypeMismatchException extends RuntimeException
{
	/**
	 */
	public static function mismatch(string $type1, string $type2): self
	{
		return new self(sprintf(
			'Aggregate type mismatch: `%s` doesn`t match the repositories type `%s`',
			$type1,
			$type2
		));
	}
}
