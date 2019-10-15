<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore;

use Psa\EventSourcing\Aggregate\AggregateRoot;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializeSerializer;
use Assert\Assert;
use DateTimeImmutable;
use PDO;
use PDOException;
use PDOStatement;
use Ramsey\Uuid\Uuid;

/**
 * PDO SQL based Snapshot Store
 *
 * Saves your aggregate state snapshot in a SQL database.
 */
class PdoSqlStore implements SnapshotStoreInterface
{
	/**
	 * PDO Instance
	 *
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * Serializer
	 *
	 * @var \Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface
	 */
	protected $serializer;

	/**
	 * Table to store the snapshots in
	 *
	 * @var string
	 */
	protected $table = 'event_store_snapshots';

	/**
	 * Constructor
	 *
	 * @param \PDO $pdo
	 * @param \Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface $serializer Serializer
	 * @param string $table Table to use
	 */
	public function __construct(
		PDO $pdo,
		?SerializerInterface $serializer = null,
		?string $table = null
	) {
		$this->pdo = $pdo;
		$this->serializer = $serializer ?? new SerializeSerializer();
		$this->table = $table === null ? 'event_store_snapshots' : $table;
	}

	/**
	 * Checks for PDO Errors
	 *
	 * @param \PDOStatement $statement Statement
	 * @return void
	 */
	protected function pdoErrorCheck(PDOStatement $statement)
	{
		if ($statement->errorCode() !== '00000') {
			$errorInfo = $statement->errorInfo();
			throw new PDOException($errorInfo[2], $errorInfo[1]);
		}
	}

	/**
	 * Stores an aggregate snapshot
	 *
	 * @param \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return void
	 */
	public function store(EventSourcedAggregateInterface $aggregate): void
	{
		$data = [
			'id' => Uuid::uuid4()->toString(),
			'aggregate_type' => get_class($aggregate),
			'aggregate_id' => $aggregate->aggregateId(),
			'aggregate_version' => $aggregate->aggregateVersion(),
			'aggregate_root' => $this->serializer->serialize($aggregate),
			'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
		];

		$sql = "INSERT INTO $this->table (`id`, `aggregate_type`, `aggregate_id`, `aggregate_version`, `aggregate_root`, `created_at`) "
			 . "VALUES (:id, :aggregate_type, :aggregate_id, :aggregate_version, :aggregate_root, :created_at)";

		$statement = $this->pdo->prepare($sql);
		$statement->execute($data);
		$this->pdoErrorCheck($statement);
	}

	/**
	 * Gets an aggregate snapshot if one exist
	 *
	 * @param string $aggregateId Aggregate Id
	 * @return null|\Psa\EventSourcing\SnapshotStore\SnapshotInterface
	 */
	public function get(string $aggregateId): ?SnapshotInterface
	{
		Assert::that($aggregateId)->uuid();

		$sql = "SELECT * FROM event_store_snapshots "
			 . "WHERE aggregate_id = :aggregateId "
			 //. "AND aggregate_type = :aggregateType"
			 . "ORDER BY aggregate_version";

		$statement = $this->pdo->prepare($sql);
		$statement->execute([
			'aggregateId' => $aggregateId,
			//'aggregateType' => null
		]);

		$this->pdoErrorCheck($statement);
		$result = $statement->fetch(PDO::FETCH_ASSOC);

		if ($result === false) {
			return null;
		}

		return $this->toSnapshot($result);
	}

	/**
	 * Turns the data array from PDO into a snapshot DTO
	 *
	 * @param array $data Data
	 * @return \Psa\EventSourcing\SnapshotStore\SnapshotInterface
	 */
	protected function toSnapshot(array $data): SnapshotInterface
	{
		return new Snapshot(
			$data['aggregate_type'],
			$data['aggregate_id'],
			$this->serializer->unserialize($data['aggregate_root']),
			(int)$data['aggregate_version'],
			DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at'])
		);
	}
}
