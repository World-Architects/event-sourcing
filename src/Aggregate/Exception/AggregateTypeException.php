<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Exception;

use Exception;

/**
 * AggregateTypeException
 */
class AggregateTypeException extends AggregateException
{
	/**
	 * @param mixed $type Type
	 * @return static
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
	 * @return static
	 */
	public static function typeMismatch(string $aggregateType, string $otherAggregateType)
	{
		return new self(sprintf(
			'Aggregate types must be equal. %s != %s',
			$aggregateType,
			$otherAggregateType
		));
	}
}
