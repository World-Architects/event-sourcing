<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\SnapshotStore\InMemoryStore;
use Psa\EventSourcing\SnapshotStore\Serializer\JsonSerializer;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializeSerializer;
use Psa\EventSourcing\SnapshotStore\SnapshotInterface;
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

		// writing
		$this->store->store($account);

		// reading
		$result = $this->store->get($id);
		$this->assertInstanceOf(SnapshotInterface::class, $result);
		$this->assertEquals($id, $result->aggregateId());
		$this->assertEquals('Psa\EventSourcing\Test\TestApp\Domain\Account', $result->aggregateType());
		$this->assertInstanceOf(DateTimeImmutable::class, $result->createdAt());

		// deleting
		$this->store->delete($id);

		$result = $this->store->get($id);
		$this->assertNull($result);
	}
}
