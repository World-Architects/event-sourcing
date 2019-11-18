<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\SnapshotStore;

use DateTimeImmutable;

/**
 * Snapshot Interface
 */
interface SnapshotInterface
{
	/**
	 * Gets the Aggregate Type
	 *
	 * @return string
	 */
	public function aggregateType(): string;

	/**
	 * Gets the aggregate UUID as string
	 *
	 * @return string
	 */
	public function aggregateId(): string;

	/**
	 * Gets the aggregate root object
	 *
	 * @return mixed
	 */
	public function aggregateRoot();

	/**
	 * Gets the latest version
	 *
	 * @return int
	 */
	public function lastVersion(): int;

	/**
	 * Gets the date the snapshot was created
	 *
	 * @return \DateTimeImmutable
	 */
	public function createdAt(): DateTimeImmutable;
}
