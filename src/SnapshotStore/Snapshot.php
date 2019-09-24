<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore;

use Assert\Assertion;
use DateTimeImmutable;

/**
 * Snapshot
 */
final class Snapshot implements SnapshotInterface
{
	/**
	 * @var string
	 */
	private $aggregateType;

	/**
	 * @var string
	 */
	private $aggregateId;

	/**
	 * @var object
	 */
	private $aggregateRoot;

	/**
	 * @var int
	 */
	private $lastVersion;

	/**
	 * @var DateTimeImmutable
	 */
	private $createdAt;

	/**
	 * Constructor
	 *
	 * @param string $aggregateType Aggregate Type
	 * @param string $aggregateId Aggregate Id
	 * @param object $aggregateRoot
	 * @param \DateTimeImmutable $createdAt Created at
	 */
	public function __construct(
		string $aggregateType,
		string $aggregateId,
		object $aggregateRoot,
		int $lastVersion,
		DateTimeImmutable $createdAt
	) {
		Assertion::notEmpty($aggregateType);
		Assertion::uuid($aggregateId, 1);
		Assertion::min($lastVersion, 1);

		$this->aggregateType = $aggregateType;
		$this->aggregateId = $aggregateId;
		$this->aggregateRoot = $aggregateRoot;
		$this->lastVersion = $lastVersion;
		$this->createdAt = $createdAt;
	}

	/**
	 * @inheritDoc
	 */
	public function aggregateType(): string
	{
		return $this->aggregateType;
	}

	/**
	 * @inheritDoc
	 */
	public function aggregateId(): string
	{
		return $this->aggregateId;
	}

	/**
	 * @inheritDoc
	 */
	public function aggregateRoot(): object
	{
		return $this->aggregateRoot;
	}

	/**
	 * @inheritDoc
	 */
	public function lastVersion(): int
	{
		return $this->lastVersion;
	}

	/**
	 * @inheritDoc
	 */
	public function createdAt(): DateTimeImmutable
	{
		return $this->createdAt;
	}
}
