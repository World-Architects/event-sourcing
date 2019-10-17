<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\SnapshotStore\InMemoryStore;
use Psa\EventSourcing\SnapshotStore\Serializer\JsonSerializer;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializeSerializer;
use Psa\EventSourcing\Test\TestApp\Domain\Account;

/**
 * In Memory Store Test
 */
class InMemoryStoreTest extends TestCase
{
	/**
	 * @var \Psa\EventSourcing\SnapshotStore\InMemoryStore
	 */
	protected $store;

	/**
	 * @inheritDoc
	 */
	public function setUp(): void
	{
		parent::setUp();

		$this->store = new InMemoryStore(new SerializeSerializer());
	}

	/**
	 * testStore
	 *
	 * @return void
	 */
	public function testStore(): void
	{
		$account = Account::create('test', 'test');
		$id = $account->aggregateId();

		$this->store->store($account);
		$this->store->get($id);
		$this->store->delete($id);
	}
}
