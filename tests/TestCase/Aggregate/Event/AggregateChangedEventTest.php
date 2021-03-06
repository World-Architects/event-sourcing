<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate\Event;

use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEvent;

/**
 * Aggregate Changed Event Test
 */
class AggregateChangedEventTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testAggregateChangedEventWithInvalidUuid(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$result = AggregateChangedEvent::occur('invalid-uuid', []);
	}

	/**
	 * @return void
	 */
	public function testAggregateChangedEvent(): void
	{
		$id = 'b46f6c31-0114-47cf-992f-7235516bee97';
		$payload = ['test' => 'payload'];

		$event = AggregateChangedEvent::occur($id, $payload);
		$this->assertInstanceOf(AggregateChangedEvent::class, $event);
		$this->assertEquals($id, $event->aggregateId());
		$this->assertEquals($payload, $event->payload());
		$this->assertEquals(1, $event->aggregateVersion());

		$this->assertEquals([
			'_aggregate_id' => 'b46f6c31-0114-47cf-992f-7235516bee97',
			'_aggregate_version' => 1
		], $event->metadata());

		$this->assertEquals('payload', $event->test());

		$event2 = $event->withAddedMetadata('added', 'this');
		$this->assertNotEquals($event, $event2);

		$this->assertEquals([
			'_aggregate_id' => 'b46f6c31-0114-47cf-992f-7235516bee97',
			'_aggregate_version' => 1,
			'added' => 'this'
		], $event2->metadata());

		$event2 = $event2->withMetadata(['over' => 'ridden']);
		$this->assertEquals(['over' => 'ridden'], $event2->metadata());
	}
}
