<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\EventStoreIntegration;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\RecordedEvent;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;
use Psa\EventSourcing\Test\TestApp\Domain\Account;
use Psa\EventSourcing\Test\TestApp\Domain\AccountId;
use Psa\EventSourcing\Test\TestApp\Domain\Event\AccountCreated;
use Psa\EventSourcing\Test\TestApp\Domain\Event\AccountUpdated;

/**
 * EventReflectionTranslatorTest
 */
class EventReflectionTranslatorTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testToStore(): void
	{
		$translator = new EventReflectionTranslator();
		$result = $translator->toStore(
			'c98ffa8f-ecda-494a-9412-ce7ff7aa0b93',
			AggregateType::fromMapping(['Account' => Account::class]),
			[
				AccountCreated::create(
					AccountId::fromString('c98ffa8f-ecda-494a-9412-ce7ff7aa0b93'),
					'Just created',
					'Just created'
				),
				AccountUpdated::create(
					AccountId::fromString('c98ffa8f-ecda-494a-9412-ce7ff7aa0b93'),
					'Changed',
					'Changed'
				)
			]
		);

		$this->assertIsArray($result);
		$this->assertCount(2, $result);
		$this->assertInstanceOf(EventData::class, $result[0]);
		$this->assertInstanceOf(EventData::class, $result[1]);

		$eventData = json_decode($result[0]->data(), true);
		$expected = [
			'accountId' => 'c98ffa8f-ecda-494a-9412-ce7ff7aa0b93',
			'name' => 'Just created',
			'description' => 'Just created'
		];
		$this->assertEquals($eventData['payload'], $expected);

		$eventData = json_decode($result[1]->data(), true);
		$expected = [
			'accountId' => 'c98ffa8f-ecda-494a-9412-ce7ff7aa0b93',
			'name' => 'Changed',
			'description' => 'Changed'
		];
		$this->assertEquals($eventData['payload'], $expected);
	}

	/**
	 * @return void
	 */
	public function testFromStore(): void
	{
		$translator = new EventReflectionTranslator();
		$event = new RecordedEvent(
			'Account-c98ffa8f-ecda-494a-9412-ce7ff7aa0b93',
			0,
			EventId::generate(),
			'Account.created',
			true,
			json_encode([
				'accountId' => 'c98ffa8f-ecda-494a-9412-ce7ff7aa0b93',
				'name' => 'Just created',
				'description' => 'Just created'
			]),
			json_encode([
				'event_class' => AccountCreated::class
			]),
			new DateTimeImmutable()
		);

		$result = $translator->fromStore($event);
		$this->assertInstanceOf(AccountCreated::class, $result);
		var_dump($result);
	}
}
