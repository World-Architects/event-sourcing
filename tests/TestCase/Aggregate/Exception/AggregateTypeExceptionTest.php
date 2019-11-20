<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeException;

/**
 * AggregateTypeExceptionTest
 */
class AggregateTypeExceptionTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testNotAnObject(): void
	{
		$result = AggregateTypeException::notAnObject('string');
		$this->assertInstanceOf(AggregateTypeException::class, $result);
		$expected = 'Aggregate root must be an object but type of string given';
		$this->assertEquals($expected, $result->getMessage());
	}

	/**
	 * @return void
	 */
	public function testTypeMissmatch(): void
	{
		$result = AggregateTypeException::typeMismatch('first', 'second');
		$this->assertInstanceOf(AggregateTypeException::class, $result);
		$expected = 'Aggregate types must be equal: `first` does not match `second`';
		$this->assertEquals($expected, $result->getMessage());
	}
}
