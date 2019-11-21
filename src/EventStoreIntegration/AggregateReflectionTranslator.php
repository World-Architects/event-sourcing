<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Assert\Assert;
use Iterator;
use InvalidArgumentException;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use RuntimeException;
use ReflectionClass;

/**
 * Aggregate Translator
 *
 * Converts domain events to whatever the store implementation expects and vice
 * versa.
 */
class AggregateReflectionTranslator implements AggregateTranslatorInterface
{
	/**
	 * @var object
	 */
	protected $aggregate;

	/**
	 * @var \ReflectionClass
	 */
	protected $reflection;

	/**
	 * @var array
	 */
	protected $propertyMap = [
		'aggregateId' => 'aggregateId',
		'aggregateVersion' => 'aggregateVersion',
		'recordedEvents' => 'recordedEvents'
	];

	/**
	 * @var array
	 */
	protected $methodeMap = [
		'reconstitute' => 'reconstituteFromHistory',
		'replay' => 'replayEvents',
		// Optional
		'aggregateId' => 'aggregateId',
		'aggregateVersion' => 'aggregateVersion',
		'recordedEvents' => 'recordedEvents'
	];

	/**
	 * @param array $propertyMap Property mapping
	 * @param array $methodMap Method mapping
	 */
	public function __construct(array $propertyMap = [], array $methodMap = [])
	{
		$this->propertyMap = array_merge($this->propertyMap, $propertyMap);
		$this->methodeMap = array_merge($this->methodeMap, $methodMap);
	}

	/**
	 * Returns the reflection for the given object
	 *
	 * @param object|string $aggregate Aggregate object
	 * @return \ReflectionClass
	 */
	protected function reflection($aggregate): ReflectionClass
	{
		if (!is_string($aggregate) && !is_object($aggregate)) {
			throw new InvalidArgumentException(sprintf(
				'Expected string or object but `%s` was given',
				gettype($aggregate)
			));
		}

		if (is_string($aggregate) && !class_exists($aggregate)) {
			throw new RuntimeException(sprintf(
				'Aggregate class `%s` does not exist',
				$aggregate
			));
		}

		$className = $aggregate;
		if (is_object($aggregate)) {
			$className = get_class($aggregate);
		}

		if ($this->reflection === null || $this->reflection->getName() !== $className) {
			$this->reflection = new ReflectionClass($aggregate);
		}

		return $this->reflection;
	}

	/**
	 * Extracts data from an object via reflecting properties and methods
	 *
	 * @param object $aggregate Aggregate
	 * @param string $propertyOrMethod Property
	 * @param array $args Arguments
	 * @return mixed
	 */
	protected function extract(object $aggregate, string $propertyOrMethod, array $args = [])
	{
		$this->reflection($aggregate);

		if (
			!isset($this->propertyMap[$propertyOrMethod])
			&& !isset($this->propertyMap[$propertyOrMethod])
		) {
			throw new RuntimeException(sprintf(
				'Property or method `%s` is not mapped',
				$propertyOrMethod
			));
		}

		if (
			isset($this->propertyMap[$propertyOrMethod])
			&& is_string($this->propertyMap[$propertyOrMethod])
		) {
			$property = $this->propertyMap[$propertyOrMethod];
			if ($this->reflection->hasProperty($property)) {
				$property = $this->reflection->getProperty($property);
				$property->setAccessible(true);

				return $property->getValue($aggregate);
			}
		}

		if (
			isset($this->methodeMap[$propertyOrMethod])
			&& is_string($this->methodeMap[$propertyOrMethod])
		) {
			$method = $this->methodeMap[$propertyOrMethod];

			if ($this->reflection->hasMethod($method)) {
				$method = $this->reflection->getMethod($method);
				$method->setAccessible(true);

				return $method->invokeArgs($aggregate, $args);
			}

			throw new RuntimeException(sprintf(
				'Method %s::%s does not exist',
				get_class($aggregate),
				$method
			));
		}

		throw new RuntimeException(sprintf(
			'Property %s does not exist',
			$property
		));
	}

	/**
	 * @param object $aggregate Aggregate
	 *
	 * @return int
	 */
	public function extractAggregateVersion(object $aggregate): int
	{
		$version = $this->extract($aggregate, 'aggregateVersion');

		Assert::that($version)
			->integer($version)
			->greaterOrEqualThan(0);

		return $version;
	}

	/**
	 * @param object $aggregate Aggregate
	 *
	 * @return string
	 */
	public function extractAggregateId(object $aggregate): string
	{
		$aggregateId = (string)$this->extract($aggregate, 'aggregateId');
		Assert::that($aggregateId)->uuid();

		return $aggregateId;
	}

	/**
	 * We need to call some public static method to reconstitute the aggregate
	 * from history.
	 *
	 * @param \Psa\EventSourcing\Aggregate\AggregateType $aggregateType Aggregate Type
	 * @param \Iterator $historyEvents History events
	 * @return object reconstructed AggregateRoot
	 */
	public function reconstituteAggregateFromHistory(
		AggregateTypeInterface $aggregateType,
		Iterator $historyEvents
	) {
		if (!$aggregateRootClass = $aggregateType->mappedClass()) {
			$aggregateRootClass = $aggregateType->toString();
		}

		$method = $this->methodeMap['reconstitute'];
		$this->reflection($aggregateRootClass);

		if (!$this->reflection->hasMethod($method)) {
			throw new RuntimeException(sprintf(
				'Method %s::%s() does not exist',
				$aggregateRootClass,
				$method
			));
		}

		return $aggregateRootClass::{$this->methodeMap['reconstitute']}($historyEvents);
	}

	/**
	 * Extracts pending events from aggregate
	 *
	 * @param object $aggregate Aggregate
	 * @return array
	 */
	public function extractPendingStreamEvents(object $aggregate): array
	{
		$reflection = $this->reflection($aggregate);

		$property = $this->propertyMap['recordedEvents'];
		if (is_string($property) && $reflection->hasProperty($property)) {
			$property = $reflection->getProperty($property);
			$property->setAccessible(true);
			$events = $property->getValue($aggregate);
			$property->setValue($aggregate, []);

			return $events;
		}

		$methodName = $this->methodeMap['recordedEvents'];
		if (is_string($methodName) && $reflection->hasMethod($methodName)) {
			$method = $reflection->getMethod($methodName);
			if ($method->isPublic()) {
				return $aggregate->{$methodName}();
			}

			$method->setAccessible(true);

			return $method->invoke($aggregate);
		}

		throw new RuntimeException(sprintf(
			'Could not extract pending events from aggregate %s',
			get_class($aggregate)
		));
	}

	/**
	 * Replay stream events on the aggregate
	 *
	 * @param object $aggregate Aggregate
	 * @param Iterator $events
	 * @return void
	 */
	public function replayStreamEvents(object $aggregate, Iterator $events): void
	{
		$method = $this->methodeMap['replay'];
		$reflection = $this->reflection($aggregate);

		if (!$reflection->hasMethod($method)) {
			throw new RuntimeException(sprintf(
				'Method %s::%s() does not exist.',
				$method,
				get_class($aggregate)
			));
		}

		$reflectionMethod = $reflection->getMethod($method);
		if (!$reflectionMethod->isPublic()) {
			$reflectionMethod->setAccessible(true);
		}

		$reflectionMethod->invokeArgs($aggregate, [$events]);
	}
}
