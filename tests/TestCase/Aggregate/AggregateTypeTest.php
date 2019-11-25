<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
use Psa\EventSourcing\Aggregate\AggregateTypeProviderInterface;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeException;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeMismatchException;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Account;

/**
 * Aggregate Type Test
 */
class AggregateTypeTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testFromAggregateToFQCN(): void
	{
		$class = new class () {};

		$result = AggregateType::fromAggregate($class);
		$this->assertEquals(get_class($class), $result->toString());
		$this->assertEquals(get_class($class), $result->mappedClass());
	}

	/**
	 * testAggregateRoot
	 *
	 * @return void
	 */
	public function testAggregateObjectWithProvider(): void
	{
		$class = new class () implements AggregateTypeProviderInterface {
			public function aggregateType(): AggregateType
			{
				return AggregateType::fromMapping([
					'Interfaced-Aggregate' => static::class
				]);
			}
		};

		$result = AggregateType::fromAggregate($class);
		$this->assertEquals('Interfaced-Aggregate', $result->toString());
		$this->assertEquals(get_class($class), $result->mappedClass());
	}

	/**
	 * testAggregateRoot
	 *
	 * @return void
	 */
	public function testAggregateObjectWithConstant(): void
	{
		$class = new class () {
			public const AGGREGATE_TYPE = 'Type-Constant';
		};

		$result = AggregateType::fromAggregate($class);
		$this->assertEquals('Type-Constant', $result->toString());
		$this->assertEquals(get_class($class), $result->mappedClass());

		$class = new class () {
			public const AGGREGATE_TYPE = ['Type-Constant' => self::class];
		};

		$result = AggregateType::fromAggregate($class);
		$this->assertEquals('Type-Constant', $result->toString());
		$this->assertEquals(get_class($class), $result->mappedClass());
	}

	/**
	 * testEquals
	 *
	 * @return void
	 */
	public function testEquals(): void
	{
		$type = AggregateType::fromMapping(['Type' => '\TypeClass']);
		$type2 = AggregateType::fromMapping(['Type' => '\OhterTypeClass']);
		$this->assertTrue($type->equals($type));
		$this->assertFalse($type->equals($type2));

		$type = AggregateType::fromString('TypeClass');
		$type2 = AggregateType::fromString('OtherTypeClass');
		$this->assertTrue($type->equals($type));
		$this->assertFalse($type->equals($type2));
	}

	/**
	 * @return void
	 */
	public function testfromAggregateClassInvalidArgumentException(): void
	{
		$this->expectException(InvalidArgumentException::class);
		AggregateType::fromAggregateClass('DoesNotExist');
	}

	/**
	 * @return void
	 */
	public function testfromAggregateClass(): void
	{
		$type = AggregateType::fromAggregateClass(Account::class);
		$this->assertInstanceOf(AggregateTypeInterface::class, $type);
	}

	/**
	 * @return void
	 */
	public function testAssert(): void
	{
		$type1 = AggregateType::fromString('One');
		$type2 = AggregateType::fromString('One');
		$type1->assert($type2);

		$this->expectException(AggregateTypeMismatchException::class);
		$type1 = AggregateType::fromString('One');
		$type2 = AggregateType::fromString('Two');
		$type1->assert($type2);
	}
}
