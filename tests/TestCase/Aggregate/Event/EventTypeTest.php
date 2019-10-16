<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate\Event;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\Event\EventTypeProviderInterface;

/**
 * Event Type Test
 */
class EventTypeTest extends TestCase
{
	/**
	 * testEventObjectWithProvider
	 *
	 * @return void
	 */
	public function testEventObjectWithProvider(): void
	{
		$class = new class() implements EventTypeProviderInterface {
			public function eventType(): EventType
			{
				return EventType::fromString('Interfaced-Event');
			}
		};

		$result = EventType::fromEvent($class);
		$this->assertEquals('Interfaced-Event', $result->toString());
	}

	/**
	 * testEventObjectWithConstant
	 *
	 * @return void
	 */
	public function testEventObjectWithConstant(): void
	{
		$class = new class() {
			const EVENT_TYPE = 'Type-Constant';
		};

		$result = EventType::fromEvent($class);
		$this->assertEquals('Type-Constant', $result->toString());
	}
}
