<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use PHPUnit\Framework\TestCase;
use PDO;
use Psa\EventSourcing\SnapshotStore\PdoSqlStore;
use Psa\EventSourcing\SnapshotStore\Serializer\JsonSerializer;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializeSerializer;
use Psa\EventSourcing\Test\TestApp\Domain\Account;

/**
 * Pdo Sql Store Test
 */
class PdoSqlStoreTest extends TestCase
{
	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * @var \Psa\EventSourcing\SnapshotStore\PdoSqlStore
	 */
	protected $store;

	/**
	 * @inheritDoc
	 */
	public function setUp(): void
	{
		parent::setUp();

		$this->pdo = $this->getMockBuilder(PDO::class)
			->disableOriginalConstructor()
			->addMethods(['execute', 'prepare'])
			->getMock();

		$this->store = new PdoSqlStore($this->pdo, new SerializeSerializer());
	}

	/**
	 * testStore
	 *
	 * @return void
	 */
	public function testStore(): void
	{
		$account = Account::create('test', 'test');
		$accountId = $account->aggregateId();

		$this->store->store($account);
		$result = $this->store->get($accountId);
		$this->store->delete($accountId);
	}
}
