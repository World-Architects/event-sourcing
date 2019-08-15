<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore;

use Psa\EventSourcing\Aggregate\AggregateRoot;
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
class InMemoryStore implements SnapshotStoreInterface
{
	/**
	 * Stores the snapshots
	 *
	 * @var array
	 */
	protected $store = [];

	/**
	 * Serializer
	 *
	 * @var \Psa\EventSourcing\SnapshotStore\Serializer\\SerializerInterface
	 */
	protected $serializer;

	/**
	 * Constructor
	 *
	 * @param \Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface $serializer Serializer
	 */
	public function __construct(
		? SerializerInterface $serializer = null
	) {
		$this->serializer = $serializer ?? new SerializeSerializer();
	}

	/**
	 * Stores an aggregate snapshot
	 *
	 * @return void
	 */
	public function store(AggregateRoot $aggregate)
	{
		$this->store[$aggregate->aggregateId()] = [
			'id' => Uuid::uuid4()->toString(),
			'aggregate_type' => get_class($aggregate),
			'aggregate_id' => $aggregate->aggregateId(),
			'aggregate_version' => $aggregate->aggregateVersion(),
			'aggregate_root' => $this->serializer->serialize($aggregate),
			'created_at' => new DateTimeImmutable()
		];
	}

	/**
	 * Gets an aggregate snapshot if one exist
	 *
	 * @return mixed
	 */
	public function get(string $aggregateId): ?SnapshotInterface
	{
		if (!isset($this->store[$aggregateId])) {
			return null;
		}

		$data = $this->store[$aggregateId];

		return new Snapshot(
			$data['aggregate_type'],
			$data['aggregate_id'],
			$this->serializer->unserialize($data['aggregate_root']),
			(int)$data['aggregate_version'],
			$data['created_at']
		);
	}
}
