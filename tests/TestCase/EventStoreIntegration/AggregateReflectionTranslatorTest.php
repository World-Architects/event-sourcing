<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\EventStoreIntegration;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Account;
use Iterator;

// phpcs:enable
class TestAggregate
{
	protected $aggregateType = [
		'TestAggregate' => TestAggregate::class
	];
	protected $aggregateId = 'e37ad7f3-91df-440c-9568-6a2b90fd7fdb';
	protected $aggregateVersion = 1;
	protected $recordedEvents = ['first' => 'event'];
	public static function reconstituteFromHistory(Iterator $historyEvents): self
	{
		return new self();
	}
	protected function replayEvents(Iterator $historyEvents): void
	{
	}
}
// phpcs:disable

/**
 * AggregateReflectionTranslatorTest
 */
class AggregateReflectionTranslatorTest extends TestCase
{
	/**
	 * testTranslator
	 *
	 * @return void
	 */
	public function testTranslator(): void
	{
		$aggregateId = 'e37ad7f3-91df-440c-9568-6a2b90fd7fdb';
		$translator = new AggregateReflectionTranslator();
		$aggregate = new TestAggregate();

		$result = $translator->extractAggregateId($aggregate);
		$this->assertEquals($result, $aggregateId);

		$result = $translator->extractAggregateVersion($aggregate);
		$this->assertEquals($result, 1);

		$result = $translator->extractPendingStreamEvents($aggregate);
		$this->assertEquals(['first' => 'event'], $result);

		$translator->replayStreamEvents(
			$aggregate,
			new ArrayIterator(['first' => 'event'])
		);

		$translator->reconstituteAggregateFromHistory(
			AggregateType::fromMapping(['TestAggregate' => TestAggregate::class]),
			new ArrayIterator(['first' => 'event'])
		);
	}
}
