<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Test\TestApp\Domain\Account;

/**
 * Aggregate Root Test
 */
class AggregateRootTest extends TestCase
{
	/**
	 * testAggregateRoot
	 */
	public function testAggregateRoot(): void
	{
		$account = Account::create(
			'Test Aggregate',
			'With a nice description'
		);

		$account->update(
			'Updated Name!',
			'Updated description'
		);

		$this->assertNotEmpty($account->aggregateId());
		$this->assertIsString($account->aggregateId());
		$this->assertEquals(2, $account->aggregateVersion());

		$result = $account->popRecordedEvents();
		$this->assertIsArray($result);
		$this->assertCount(2, $result);
	}
}
