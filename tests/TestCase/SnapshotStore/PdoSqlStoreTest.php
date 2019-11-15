<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use DateTimeImmutable;
use PDO;
use Psa\EventSourcing\SnapshotStore\PdoSqlStore;
use Psa\EventSourcing\SnapshotStore\Serializer\JsonSerializer;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializeSerializer;
use Psa\EventSourcing\SnapshotStore\Snapshot;
use Psa\EventSourcing\SnapshotStore\SnapshotInterface;
use Psa\EventSourcing\Test\TestApp\Domain\Account;
use Psa\EventSourcing\Test\TestCase\TestCase;

/**
 * PDO Sql Store Test
 */
class PdoSqlStoreTest extends TestCase
{
	/**
	 * @var array
	 */
	protected $sqlFixtures = [
		'EventStoreSnapshots'
	];

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

		$this->store = new PdoSqlStore(
			$this->pdoTestConnection,
			new SerializeSerializer()
		);
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

		$snapshot = new Snapshot(
			get_class($account),
			$account->aggregateId(),
			$account,
			$account->aggregateVersion(),
			new DateTimeImmutable()
		);

		$this->store->store($snapshot);

		$result = $this->store->get($accountId);
		$this->assertInstanceOf(SnapshotInterface::class, $result);

		$this->store->delete($accountId);
	}
}
