<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use PHPUnit\Framework\TestCase;
use PDO;
use Psa\EventSourcing\SnapshotStore\PdoSqlStore;
use Psa\EventSourcing\SnapshotStore\Serializer\JsonSerializer;

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
			->getMock();

		$this->store = new PdoSqlStore($this->pdo, new JsonSerializer());
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
