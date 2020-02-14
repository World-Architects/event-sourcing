<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate\Event;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException;

/**
 * EventTypeException
 */
class EventTypeExceptionTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testNotAnObject(): void
	{
		$result = EventTypeException::notAnObject('string');
		$this->assertInstanceOf(EventTypeException::class, $result);
		$expected = 'Event type must be an object but type of string given';
		$this->assertEquals($expected, $result->getMessage());
	}

	/**
	 * @return void
	 */
	public function testTypeMissmatch(): void
	{
		$result = EventTypeException::typeMismatch('first', 'second');
		$this->assertInstanceOf(EventTypeException::class, $result);
		$expected = 'Event types must be equal: `first` does not match `second`';
		$this->assertEquals($expected, $result->getMessage());
	}

	/**
	 * @return void
	 */
	public function testMappingFailed(): void
	{
		$result = EventTypeException::mappingFailed('\Some\Class', 12, '1234');
		$this->assertInstanceOf(EventTypeException::class, $result);
		$expected = 'Mapping of \Some\Class failed. Version 12 ID 1234';
		$this->assertEquals($expected, $result->getMessage());
	}
}
