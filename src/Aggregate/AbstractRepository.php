<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

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
	 * @throws \Psa\EventSourcing\Aggregate\Exception\AggregateTypeException
	 * @param object $eventSourcedAggregateRoot Event Sourced Aggregate Root
	 * @return void
	 */
	protected function assertAggregateType(object $eventSourcedAggregateRoot): void
	{
		$aggregateType = AggregateType::fromAggregate($eventSourcedAggregateRoot);
		$this->aggregateType->assert($aggregateType);
	}

	/**
	 * Default stream name generation.
	 *
	 * Override this method in an extending repository to provide a custom name
	 *
	 * @param string $aggregateId Aggregate UUID as string
	 * @return string
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
