<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\SnapshotStore;

use Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializeSerializer;
use DateTimeImmutable;

/**
 * In Memory Store
 *
 * Saves your aggregate state snapshot in memory.
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
	 * @var \Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface
	 */
	protected $serializer;

	/**
	 * Constructor
	 *
	 * @param \Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface $serializer Serializer
	 */
	public function __construct(
		?SerializerInterface $serializer = null
	) {
		$this->serializer = $serializer ?: new SerializeSerializer();
	}

	/**
	 * @inheritDoc
	 */
	public function store(SnapshotInterface $snapshot): void
	{
		$this->store[$snapshot->aggregateId()] = [
			'aggregate_type' => $snapshot->aggregateType(),
			'aggregate_id' => $snapshot->aggregateId(),
			'aggregate_version' => $snapshot->lastVersion(),
			'aggregate_root' => $this->serializer->serialize($snapshot->aggregateRoot()),
			'created_at' => $snapshot->createdAt()->format('Y-m-d H:i:s')
		];
	}

	/**
	 * @inheritDoc
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
			DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at'])
		);
	}

	/**
	 * @inheritDoc
	 */
	public function delete(string $aggregateId): void
	{
		unset($this->store[$aggregateId]);
	}
}
