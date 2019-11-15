<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

use Assert\Assert;
use Psa\EventSourcing\Aggregate\Exception\AggregateTypeException;
use InvalidArgumentException;

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
	 * @param object $eventSourcedAggregateRoot
	 * @throws Exception\AggregateTypeException
	 */
	public static function fromAggregateRoot(object $eventSourcedAggregateRoot): AggregateType
	{
		// Check if the aggregate implements the type provider
		if ($eventSourcedAggregateRoot instanceof AggregateTypeProviderInterface) {
			return $eventSourcedAggregateRoot->aggregateType();
		}

		$self = new static();
		$aggregateClass = get_class($eventSourcedAggregateRoot);
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
	public static function fromAggregateRootClass(string $aggregateRootClass): AggregateType
	{
		if (!class_exists($aggregateRootClass)) {
			throw new InvalidArgumentException(sprintf('Aggregate root class %s can not be found', $aggregateRootClass));
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
	public static function fromString(string $aggregateTypeString): AggregateType
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
	public static function fromMapping(array $mapping): AggregateType
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
	public function assert(object $aggregateRoot): void
	{
		$otherAggregateType = self::fromAggregateRoot($aggregateRoot);

		if (!$this->equals($otherAggregateType)) {
			throw AggregateTypeException::typeMismatch(
				$this->toString(),
				$otherAggregateType->toString()
			);
		}
	}

	/**
	 * Checks if two instances of this class are equal
	 *
	 * @return bool
	 */
	public function equals(AggregateType $other): bool
	{
		if (!$aggregateTypeString = $this->mappedClass()) {
			$aggregateTypeString = $this->toString();
		}

		return $aggregateTypeString === $other->toString()
			|| $aggregateTypeString === $other->mappedClass();
	}
}
