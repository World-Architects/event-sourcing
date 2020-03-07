<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Exception;

/**
 * AggregateTypeException
 */
class AggregateTypeException extends AggregateException
{
	/**
	 * @param mixed $type Type
	 * @return self
	 */
	public static function notAnObject($type)
	{
		return new self(sprintf(
			'Aggregate root must be an object but type of %s given',
			gettype($type)
		));
	}

	/**
	 * @param string $aggregateType Aggregate Type
	 * @param string $otherAggregateType Other Aggregate Type
	 * @return self
	 */
	public static function typeMismatch(string $aggregateType, string $otherAggregateType)
	{
		return new self(sprintf(
			'Aggregate types must be equal: `%s` does not match `%s`',
			$aggregateType,
			$otherAggregateType
		));
	}
}
