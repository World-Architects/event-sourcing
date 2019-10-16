<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\SnapshotStore\InMemoryStore;
use Psa\EventSourcing\SnapshotStore\Serializer\JsonSerializer;

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

		$this->store = new InMemoryStore(new JsonSerializer());
	}

	/**
	 * testStore
	 *
	 * @return void
	 */
	public function testStore(): void
	{
	}

	/**
	 * testGet
	 *
	 * @return void
	 */
	public function testGet(): void
	{
	}

	/**
	 * testDelete
	 *
	 * @return void
	 */
	public function testDelete(): void
	{
	}
}
