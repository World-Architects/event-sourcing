<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use Exception;

class AggregateTypeException extends AggregateException
{
	public static function notAnObject($type)
	{
		return new self(sprintf(
			'Aggregate root must be an object but type of %s given',
			gettype($type)
		));
	}

	public static function typeMismatch(string $aggregateType, string $otherAggregateType)
	{
		return new self(sprintf(
			'Aggregate types must be equal. %s != %s', $aggregateType, $otherAggregateType
		));
	}
}
