<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\EventSourcing\SnapshotStore;

use Psa\EventSourcing\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Psa\EventSourcing\EventSourcing\SnapshotStore\Serializer\SerializerInterface;
use Cache\Adapter\Common\CacheItem;
use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;

/**
 * PsrCache Pool Store
 */
class PsrCacheItemPoolStore
{
	/**
	 * @var \Psr\Cache\CacheItemPoolInterface
	 */
	protected $cacheItemPool;

	/**
	 * Serializer
	 *
	 * @var \Psa\EventSourcing\EventSourcing\SnapshotStore\Serializer\SerializerInterface
	 */
	protected $serializer;

	/**
	 * Constructor
	 *
	 * @param \Psr\Cache\CacheItemPoolInterface $cacheItemPool PSE Cache Item Pool
	 * @param \Psa\EventSourcing\EventSourcing\SnapshotStore\Serializer\SerializerInterface|null $serializer Serializer
	 */
	public function __construct(
		CacheItemPoolInterface $cacheItemPool,
		?SerializerInterface $serializer = null
	) {
		$this->cacheItemPool = $cacheItemPool;
		$this->serializer = $serializer;
	}

	/**
	 * @inheritDoc
	 */
	public function store(EventSourcedAggregateInterface $aggregate)
	{
		$item = (new CacheItem($aggregate->aggregateId()))->set([
			'aggregate_id' => $aggregate->aggregateId(),
			'aggregate_type' => get_class($aggregate),
			'aggregate_version' => $aggregate->aggregateVersion(),
			'aggregate_root' => $this->serializer->serialize($aggregate),
			'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
		]);

		$this->cacheItemPool->save($item);
	}

	/**
	 * @inheritDoc
	 */
	public function get(string $aggregateId)
	{
		if (!$this->cacheItemPool->hasItem($aggregateId)) {
			return null;
		}

		$data = $this->cacheItemPool->getItem($aggregateId);

		return new Snapshot(
			$data['aggregate_type'],
			$data['aggregate_id'],
			$this->serializer->unserialize($data['aggregate_root']),
			(int)$data['aggregate_version'],
			DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_at'])
		);
	}
}
