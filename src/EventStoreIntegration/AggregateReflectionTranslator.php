<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use RuntimeException;
use ReflectionClass;

/**
 * Aggregate Translator
 *
 * Converts domain events to whatever the store implementation expects and vice
 * versa.
 */
final class AggregateReflectionTranslator implements AggregateTranslatorInterface
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
		'events' => 'events'
	];

	/**
	 * @var array
	 */
	protected $methodeMap = [
		'reconstitute' => 'reconstituteFromHistory',
		'replay' => 'replay',
		// Optional
		'aggregateId' => 'aggregateId',
		'aggregateVersion' => 'aggregateVersion',
		'events' => 'events'
	];

	/**
	 * @param array $map Map
	 */
	public function __construct(array $propertyMap = [], array $methodMap = [])
	{
		$this->propertyMap = array_merge($this->propertyMap, $propertyMap);
		$this->methodeMap = array_merge($this->methodeMap, $methodMap);
	}

	/**
	 * @param object
	 * @return \ReflectionClass
	 */
	protected function reflection($aggregate): ReflectionClass
	{
		$className = $aggregate;
		if (is_object($aggregate)) {
			$className = get_class($aggregate);
		}

		if (!$this->reflection || $this->reflection->getName() !== $className) {
			$this->reflection = new ReflectionClass($aggregate);
		}

		return $this->reflection;
	}

	/**
	 * @param object $aggregate Aggregate
	 * @param string $property Property
	 */
	protected function extract($aggregate, string $propertyOrMethod, array $args = [])
	{
		$this->reflection($aggregate);

		if (!isset($this->propertyMap[$propertyOrMethod]) && !isset($this->propertyMap[$propertyOrMethod . 'Method'])) {
			throw new RuntimeException(sprintf(
				'Property or method %s not mapped',
				$propertyOrMethod
			));
		}

		$property = $this->propertyMap[$propertyOrMethod];

		if ($this->reflection->hasProperty($property)) {
			$property = $this->reflection->getProperty($property);
			$property->setAccessible(true);

			return $property->getValue($aggregate);
		}

		if (isset($this->methodeMap[$propertyOrMethod])) {
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
	 * @param object $eventSourcedAggregateRoot
	 *
	 * @return int
	 */
	public function extractAggregateVersion($aggregate): int
	{
		return $this->extract($aggregate, 'aggregateVersion');
	}

	/**
	 * @param object $anEventSourcedAggregateRoot
	 *
	 * @return string
	 */
	public function extractAggregateId($aggregate): string
	{
		return $this->extract($aggregate, 'aggregateId');
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
		AggregateType $aggregateType,
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

		$aggregateRootClass::{$this->methodeMap['reconstitute']}($historyEvents);
	}

	/**
	 * @param object $anEventSourcedAggregateRoot
	 *
	 * @return array
	 */
	public function extractPendingStreamEvents($aggregate): array
	{
		return $this->extract($aggregate, 'events');
	}

	/**
	 * @param object $anEventSourcedAggregateRoot
	 * @param Iterator $events
	 * @return void
	 */
	public function replayStreamEvents($aggregate, Iterator $events): void
	{
		$method = $this->methodeMap['replay'];
		$reflection = $this->reflection($aggregate);

		if (!$reflection->hasMethod($method)) {
			throw new RuntimeException(sprintf(
				'Method %s::%s() does not exist.',
				$method,
				get_class($aggregate),
			));
		}

		$reflectionMethod = $reflection->getMethod($method);
		if (!$reflectionMethod->isPublic()) {
			$reflectionMethod->setAccessible(true);
		}

		$reflectionMethod->invokeArgs($aggregate, [$events]);
	}
}
