<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use ArrayIterator;
use Assert\Assert;
use DateTimeImmutable;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeMismatchException;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Event\Exception\EventTypeException;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslatorInterface;
use Psa\EventSourcing\EventStoreIntegration\AggregateChangedEventTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventTranslatorInterface;
use Psa\EventSourcing\SnapshotStore\Snapshot;
use Psa\EventSourcing\SnapshotStore\SnapshotInterface;
use Psa\EventSourcing\SnapshotStore\SnapshotStoreInterface;
use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\EventStoreConnection;
use Prooph\EventStore\ExpectedVersion;
use Prooph\EventStore\SliceReadStatus;
use Prooph\EventStore\StreamEventsSlice;
use RuntimeException;

/**
 * Abstract Aggregate Repository
 *
 * When extending this class make sure you are setting the aggregate type
 * property with your aggregate type the repository should use.
 *
 * Alternatively, depending on your flavor and style, you can also declare the
 * AGGREGATE_TYPE constant. A recommended way of doing so is to re-use the
 * constant from your aggregate:
 *
 * const AGGREGATE_TYPE = SomeAggregate::AGGREGATE_TYPE;
 *
 * The third possibility is to implement the AggregateTypeProviderInterface.
 */
abstract class AbstractRepository implements AggregateRepositoryInterface
{
	/**
	 * Aggregate Type
	 *
	 * @var \Psa\EventSourcing\Aggregate\AggregateTypeInterface
	 */
	protected $aggregateType;

	/**
	 * Determines and checks the aggregate type for this repository
	 *
	 * @return void
	 */
	protected function determineAggregateType(): void
	{
		if (defined('static::AGGREGATE_TYPE')) {
			/** @phpstan-ignore PHPStan.Rules */
			$this->aggregateType = static::AGGREGATE_TYPE;
		}

		if ($this instanceof AggregateTypeProviderInterface) {
			$this->aggregateType = $this->aggregateType();
			return;
		}

		if (is_array($this->aggregateType)) {
			$this->aggregateType = AggregateType::fromMapping($this->aggregateType);
			return;
		}

		if (!$this->aggregateType instanceof AggregateTypeInterface) {
			throw new RuntimeException(sprintf(
				'%s::$aggregateType could not resolve to %s. %s was provided.',
				self::class,
				AggregateTypeInterface::class,
				is_object($this->aggregateType)
					? get_class($this->aggregateType)
					: gettype($this->aggregateType)
			));
		}
	}

	/**
	 * @param object $eventSourcedAggregateRoot
	 */
	protected function assertAggregateType(object $eventSourcedAggregateRoot)
	{
		$aggregateType = AggregateType::fromAggregate($eventSourcedAggregateRoot);
		$this->aggregateType->assert($aggregateType);
	}

	/**
	 * Default stream name generation.
	 *
	 * Override this method in an extending repository to provide a custom name
	 */
	protected function determineStreamName(string $aggregateId): string
	{
		if ($this->streamName === null) {
			$prefix = (string)$this->aggregateType;
			$prefix = str_replace('\\', '', $prefix);
		} else {
			$prefix = $this->streamName;
		}

		return $prefix . '-' . $aggregateId;
	}
}
