<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\EventStoreIntegration;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventData;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\EventStoreIntegration\AggregateChangedEventTranslator;
use Psa\EventSourcing\Test\TestApp\Domain\Account;

/**
 * AggregateChangedEventTranslatorTest
 */
class AggregateChangedEventTranslatorTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testToStore(): void
	{
		$account = Account::create(
			'Test Aggregate',
			'With a nice description'
		);

		$account->update(
			'Updated Name!',
			'Updated description'
		);

		$translator = new AggregateChangedEventTranslator();

		$result = $translator->toStore(
			$account->aggregateId(),
			AggregateType::fromString('Account'),
			$account->popRecordedEvents()
		);

		$this->assertIsArray($result);
		$this->assertCount(2, $result);
		$this->assertInstanceOf(EventData::class, $result[0]);
		$this->assertInstanceOf(EventData::class, $result[1]);

		/**
		 * @var \Prooph\EventStore\EventData $event
		 */
		$event = $result[0];
		$this->assertEquals('Accounting.Account.created', $event->eventType());
		$this->assertTrue($event->isJson());

		$expected = [
			'accountId' => $account->aggregateId(),
			'name' => 'Test Aggregate',
			'description' => 'With a nice description'
		];
		$this->assertEquals($expected, json_decode($event->data(), true));

		/**
		 * @var \Prooph\EventStore\EventData $event
		 */
		$event = $result[1];
		$this->assertEquals('Accounting.Account.updated', $event->eventType());
		$this->assertTrue($event->isJson());

		$expected = [
			'accountId' => $account->aggregateId(),
			'name' => 'Updated Name!',
			'description' => 'Updated description'
		];
		$this->assertEquals($expected, json_decode($event->data(), true));
	}
}
