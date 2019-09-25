<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Async\EventStoreConnection;
use Psa\EventSourcing\Test\TestApp\Domain\Account;
use Psa\EventSourcing\Test\TestApp\Domain\Repository\AccountRepository;

/**
 * Abstract Aggregate Repository Test
 */
class AbstractAggregateRepositoryTest extends TestCase
{
	/**
	 * @inheritDoc
	 */
	public function setUp(): void
	{
		parent::setUp();

		$this->eventStore = $this->getMockBuilder(EventStoreConnection::class)
			->disableOriginalConstructor()
			->getMock();

		$this->repository = new AccountRepository($this->eventStore);
	}

	/**
	 * testSaveAggregate
	 *
	 * @return void
	 */
	public function testSaveAggregate(): void
	{
		$account = Account::create(
			'Name',
			'Description'
		);

		$this->repository->saveAggregate($account);

		$result = $this->repository->getAggregate($account->aggregateId());
	}
}
