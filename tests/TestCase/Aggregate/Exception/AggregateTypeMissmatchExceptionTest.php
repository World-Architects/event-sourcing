<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeMismatchException;

/**
 * AggregateTypeMissmatchExceptionTest
 */
class AggregateTypeMissmatchExceptionTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testMismatch(): void
	{
		$result = AggregateTypeMismatchException::mismatch('first', 'second');
		$this->assertInstanceOf(AggregateTypeMismatchException::class, $result);
		$expected = 'Aggregate type mismatch: `first` doesn`t match the repositories type `second`';
		$this->assertEquals($expected, $result->getMessage());
	}
}
