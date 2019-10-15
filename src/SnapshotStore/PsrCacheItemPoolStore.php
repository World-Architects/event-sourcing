<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore;

use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use Psa\EventSourcing\SnapshotStore\Cache\CacheItemFactoryInterface;
use Psa\EventSourcing\SnapshotStore\Cache\PhpCacheFactory;
use Psa\EventSourcing\SnapshotStore\Serializer\JsonSerializer;
use Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface;
use Cache\Adapter\Common\CacheItem;
use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * PsrCache Pool Store
 *
 * Uses a PSR Cache Pool to store snapshots in a cache
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
	 * @var \Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface
	 */
	protected $serializer;

	/**
	 * @var \Psa\EventSourcing\SnapshotStore\Cache\CacheItemFactoryInterface
	 */
	protected $cacheItemFactory;

	/**
	 * Constructor
	 *
	 * @param \Psr\Cache\CacheItemPoolInterface $cacheItemPool PSE Cache Item Pool
	 * @param \Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface|null $serializer Serializer
	 */
	public function __construct(
		CacheItemPoolInterface $cacheItemPool,
		?SerializerInterface $serializer = null,
		?CacheItemFactoryInterface $cacheItemFactory = null
	) {
		$this->cacheItemPool = $cacheItemPool;
		$this->serializer = $serializer ?? new JsonSerializer();
		$this->cacheItemFactory = $cacheItemFactory ?? new PhpCacheFactory();
	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface $aggregate Aggregate
	 * @return \Psr\Cache\CacheItemInterface
	 */
	protected function buildCacheItem(EventSourcedAggregateInterface $aggregate): CacheItemInterface
	{
		return $this->cacheItemFactory->buildCacheItem(
			$aggregate->aggregateId(),
			get_class($aggregate),
			$aggregate->aggregateVersion(),
			$this->serializer->serialize($aggregate),
			(new DateTimeImmutable())
		);
	}

	/**
	 * @inheritDoc
	 */
	public function store(EventSourcedAggregateInterface $aggregate)
	{
		$this->cacheItemPool->save($this->buildCacheItem($aggregate));
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
