<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate\Event;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\Event\EventTypeProviderInterface;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeMismatchException;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Account;
use Psa\EventSourcing\Test\TestApp\Domain\ReflectionBased\AccountId;
use Psa\EventSourcing\Test\TestApp\Domain\ReflectionBased\Event\AccountCreated;

/**
 * Event Type Test
 */
class EventTypeTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testfromEvent(): void
	{
		$event = AccountCreated::create(
			AccountId::generate(),
			'test',
			'test'
		);
		$result = EventType::fromEvent($event);

		$this->assertEquals('Accounting.Account.created', $result->toString());
		$this->assertEquals('Accounting.Account.created', (string)$result);
		$this->assertEquals(AccountCreated::class, $result->mappedClass());
	}

	/**
	 * @return void
	 */
	public function testFromEventFQCNFallback(): void
	{
		$class = new \ArrayIterator([]);
		$result = EventType::fromEvent($class);

		$this->assertEquals(\ArrayIterator::class, (string)$result);
		$this->assertEquals(\ArrayIterator::class, $result->mappedClass());
	}

	/**
	 * @return void
	 */
	public function testFromEventClass(): void
	{
		$result = EventType::fromEventClass(AccountCreated::class);

		$this->assertEquals(AccountCreated::class, $result->toString());
		$this->assertEquals(AccountCreated::class, (string)$result);
		$this->assertEquals(AccountCreated::class, $result->mappedClass());
	}

	/**
	 * testEventObjectWithProvider
	 *
	 * @return void
	 */
	public function testEventObjectWithProvider(): void
	{
		$class = new class () implements EventTypeProviderInterface {
			public function eventType(): EventType
			{
				return EventType::fromString('Interfaced-Event');
			}
		};

		$result = EventType::fromEvent($class);
		$this->assertEquals('Interfaced-Event', $result->toString());
		$this->assertEquals('Interfaced-Event', (string)$result);
		$this->assertNull($result->mappedClass());
	}

	/**
	 * testEventObjectWithConstant
	 *
	 * @return void
	 */
	public function testEventObjectWithConstant(): void
	{
		$class = new class () {
			public const EVENT_TYPE = 'Type-Constant';
		};

		$result = EventType::fromEvent($class);
		$this->assertEquals('Type-Constant', $result->toString());
		$this->assertEquals('Type-Constant', (string)$result);
		$this->assertNotNull($result->mappedClass());
	}

	/**
	 * testVoid
	 *
	 * @return void
	 */
	public function testEquals(): void
	{
		$event = $event2 = EventType::fromString('\Test\Class');
		$event3 = EventType::fromString('\Test\Other\Class');

		$this->assertTrue($event->equals($event2));
		$this->assertFalse($event->equals($event3));
	}

	/**
	 * testFromMapping
	 *
	 * @return void
	 */
	public function testFromMapping(): void
	{
		$type = EventType::fromMapping(['Account' => Account::class]);
		$this->assertEquals(Account::class, $type->mappedClass());
	}

	/**
	 * testFromStringException
	 *
	 * @return void
	 */
	public function testFromStringException(): void
	{
		$this->expectException(InvalidArgumentException::class);
		EventType::fromString('');
	}

	/**
	 * testFromEventClass
	 *
	 * @return void
	 */
	public function testFromEventClassInvalidArgumentException(): void
	{
		$this->expectException(InvalidArgumentException::class);
		EventType::fromEventClass('DoesNotExist');
	}

	/**
	 * @return void
	 */
	public function testAssert(): void
	{
		$event = AccountCreated::create(
			AccountId::generate(),
			'test',
			'test'
		);
		$type1 = EventType::fromEvent($event);
		$type2 = EventType::fromEvent($event);
		$type1->assert($type2);

		$this->expectException(EventTypeMismatchException::class);
		$type1 = EventType::fromString('One');
		$type2 = EventType::fromString('Two');
		$type1->assert($type2);
	}
}
