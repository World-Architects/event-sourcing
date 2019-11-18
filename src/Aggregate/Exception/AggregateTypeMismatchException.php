<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Exception;

use RuntimeException;

/**
 * AggregateTypeMismatchException
 */
class AggregateTypeMismatchException extends AggregateTypeException
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
