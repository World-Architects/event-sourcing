<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event;

use Assert\Assert;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;

/**
 * Aggregate Changed Event Interface
 */
interface AggregateChangedEventInterface
{
	/**
	 * @return static
	 */
	public static function occur(
		string $aggregateId,
		array $payload = []
	): AggregateChangedEventInterface;

	/**
	 * Gets the meta data
	 *
	 * @return array
	 */
	public function metadata(): array;

	/**
	 * Gets the aggregate UUID as string
	 *
	 * @return string
	 */
	public function aggregateId(): string;

	/**
	 * Return message payload as array
	 *
	 * The payload should only contain scalar types and sub arrays.
	 * The payload is normally passed to json_encode to persist the message or
	 * push it into a message queue.
	 */
	public function payload(): array;

	/**
	 * With meta data
	 *
	 * @return \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface
	 */
	public function withMetadata(array $metadata): AggregateChangedEventInterface;

	/**
	 * Returns new instance of message with $key => $value added to metadata
	 *
	 * Given value must have a scalar or array type.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface
	 */
	public function withAddedMetadata(string $key, $value): AggregateChangedEventInterface;

	/**
	 * Gets the aggregate version
	 *
	 * @return int
	 */
	public function aggregateVersion(): int;

	/**
	 * With aggregate version
	 *
	 * @param int $version Version
	 * @return \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface
	 */
	public function withAggregateVersion(int $version): AggregateChangedEventInterface;
}
