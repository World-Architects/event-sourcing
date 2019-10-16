<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\AggregateTypeProviderInterface;

/**
 * Aggregate Type Test
 */
class AggregateTypeTest extends TestCase
{
	/**
	 * testAggregateRoot
	 *
	 * @return void
	 */
	public function testAggregateObjectWithProvider(): void
	{
		$class = new class() implements AggregateTypeProviderInterface {
			public function aggregateType(): AggregateType
			{
				return AggregateType::fromString('Interfaced-Aggregate');
			}
		};

		$result = AggregateType::fromAggregateRoot($class);
		$this->assertEquals('Interfaced-Aggregate', $result->toString());
	}

	/**
	 * testAggregateRoot
	 *
	 * @return void
	 */
	public function testAggregateObjectWithConstant(): void
	{
		$class = new class() {
			const AGGREGATE_TYPE = 'Type-Constant';
		};

		$result = AggregateType::fromAggregateRoot($class);
		$this->assertEquals('Type-Constant', $result->toString());
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
}
