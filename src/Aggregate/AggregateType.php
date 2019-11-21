<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use Assert\Assert;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeException;
use InvalidArgumentException;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeMismatchException;

/**
 * Aggregate Type
 */
class AggregateType implements AggregateTypeInterface
{
	/**
	 * @var string|null
	 */
	protected $aggregateType;

	/**
	 * @var array
	 */
	protected $mapping = [];

	/**
	 * @var string
	 */
	protected $aggregateTypeConstant = 'AGGREGATE_TYPE';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	private function __construct()
	{
	}

	/**
	 * Use this factory when aggregate type should be detected based on given aggregate root
	 *
	 * @param object $aggregateRoot Aggregate
	 * @throws Exception\AggregateTypeException
	 */
	public static function fromAggregate(object $aggregateRoot): AggregateTypeInterface
	{
		// Check if the aggregate implements the type provider
		if ($aggregateRoot instanceof AggregateTypeProviderInterface) {
			return $aggregateRoot->aggregateType();
		}

		$self = new static();
		$aggregateClass = get_class($aggregateRoot);
		$typeConstant = $aggregateClass . '::' . $self->aggregateTypeConstant;

		// Check if the aggregate has the type defined as constant
		if (defined($typeConstant)) {
			$self->aggregateType = constant($typeConstant);
			$self->mapping = [$self->aggregateType => $aggregateClass];

			return $self;
		}

		// Fall back to the FQCN as type
		$self->aggregateType = $aggregateClass;
		$self->mapping = [$aggregateClass => $aggregateClass];

		return $self;
	}

	/**
	 * Use this factory when aggregate type equals to aggregate root class
	 * The factory makes sure that the aggregate root class exists.
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function fromAggregateClass(string $aggregateRootClass): AggregateTypeInterface
	{
		if (!class_exists($aggregateRootClass)) {
			throw new InvalidArgumentException(sprintf(
				'Aggregate root class %s can not be found',
				$aggregateRootClass
			));
		}

		$self = new static();
		$self->aggregateType = $aggregateRootClass;

		return $self;
	}

	/**
	 * Use this factory when the aggregate type is not equal to the aggregate root class
	 *
	 * @param string $aggregateTypeString Aggregate Type String
	 * @throws \InvalidArgumentException
	 */
	public static function fromString(string $aggregateTypeString): AggregateTypeInterface
	{
		Assert::that($aggregateTypeString)->string()->notBlank();

		$self = new static();
		$self->aggregateType = $aggregateTypeString;

		return $self;
	}

	/**
	 * @param array $mapping Mapping
	 * @return static
	 */
	public static function fromMapping(array $mapping): AggregateTypeInterface
	{
		$self = new static();
		$self->mapping = $mapping;

		return $self;
	}

	/**
	 * @return null|string
	 */
	public function mappedClass(): ?string
	{
		return empty($this->mapping) ? null : current($this->mapping);
	}

	/**
	 * @return string
	 */
	public function toString(): string
	{
		return empty($this->mapping) ? (string)$this->aggregateType : (string)key($this->mapping);
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->toString();
	}

	/**
	 * @param object $aggregateRoot An aggregate
	 * @throws Exception\AggregateTypeException
	 */
	public function assert(AggregateTypeInterface $otherType): void
	{
		if (!$this->equals($otherType)) {
			throw AggregateTypeMismatchException::mismatch(
				(string)$this,
				(string)$otherType
			);
		}
	}

	/**
	 * Checks if two instances of this class are equal
	 *
	 * @return bool
	 */
	public function equals(AggregateTypeInterface $other): bool
	{
		if (!$typeString = $this->mappedClass()) {
			$typeString = $this->toString();
		}

		return $typeString === $other->toString()
			|| $typeString === $other->mappedClass();
	}
}
