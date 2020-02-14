<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use PHPUnit\Framework\TestCase;
use ArrayIterator;
use Psa\EventSourcing\Aggregate\Exception\MissingEventHandlerException;

/**
 * MissingEventHandlerExceptionTest
 */
class MissingEventHandlerExceptionTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testMissingFor(): void
	{
		// Just use a random class to get an object
		$object = new ArrayIterator([]);
		$result = MissingEventHandlerException::missingFor($object, 'hanlder');
		$this->assertInstanceOf(MissingEventHandlerException::class, $result);
		$expected = 'Missing event handler method `hanlder` for `ArrayIterator`';
		$this->assertEquals($expected, $result->getMessage());
	}
}
