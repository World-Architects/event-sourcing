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
	}
}
