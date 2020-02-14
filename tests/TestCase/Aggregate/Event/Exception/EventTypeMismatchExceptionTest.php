<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeMismatchException;

/**
 * EventTypeMismatchExceptionTest
 */
class EventTypeMismatchExceptionTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testMismatch(): void
	{
		$result = EventTypeMismatchException::mismatch('first', 'second');
		$this->assertInstanceOf(EventTypeMismatchException::class, $result);
		$expected = 'Event type mismatch: `first` doesn`t match `second`';
		$this->assertEquals($expected, $result->getMessage());
	}
}
