<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;

/**
 * Aggregate Translator
 *
 * Converts domain events to whatever the store implementation expects and vice
 * versa.
 */
final class AggregateTranslator implements AggregateTranslatorInterface
{
	/**
	 * @var AggregateRootDecorator
	 */
	protected $aggregateRootDecorator;

	/**
	 * @param object $eventSourcedAggregateRoot
	 *
	 * @return int
	 */
	public function extractAggregateVersion($eventSourcedAggregateRoot): int
	{
		return $this->getAggregateRootDecorator()
			->extractAggregateVersion($eventSourcedAggregateRoot);
	}

	/**
	 * @param object $anEventSourcedAggregateRoot
	 *
	 * @return string
	 */
	public function extractAggregateId($anEventSourcedAggregateRoot): string
	{
		return $this->getAggregateRootDecorator()
			->extractAggregateId($anEventSourcedAggregateRoot);
	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\AggregateType $aggregateType Aggregate Type
	 * @param \Iterator $historyEvents
	 * @return object reconstructed AggregateRoot
	 */
	public function reconstituteAggregateFromHistory(
		AggregateType $aggregateType,
		Iterator $historyEvents
	) {
		if (!$aggregateRootClass = $aggregateType->mappedClass()) {
			$aggregateRootClass = $aggregateType->toString();
		}

		return $this->getAggregateRootDecorator()
			->fromHistory($aggregateRootClass, $historyEvents);
	}

	/**
	 * @param object $anEventSourcedAggregateRoot
	 *
	 * @return array
	 */
	public function extractPendingStreamEvents($anEventSourcedAggregateRoot): array
	{
		return $this->getAggregateRootDecorator()
			->extractRecordedEvents($anEventSourcedAggregateRoot);
	}

	/**
	 * @param object $anEventSourcedAggregateRoot
	 * @param Iterator $events
	 * @return void
	 */
	public function replayStreamEvents($anEventSourcedAggregateRoot, Iterator $events): void
	{
		$this->getAggregateRootDecorator()
			->replayStreamEvents($anEventSourcedAggregateRoot, $events);
	}

	/**
	 * @return object
	 */
	public function getAggregateRootDecorator()
	{
		if ($this->aggregateRootDecorator === null) {
			$this->aggregateRootDecorator = AggregateRootDecorator::newInstance();
		}

		return $this->aggregateRootDecorator;
	}

	/**
	 * Sets the aggregate decorator
	 *
	 * @param object $anAggregateRootDecorator A decorator
	 * @return void
	 */
	public function setAggregateRootDecorator($anAggregateRootDecorator): void
	{
		$this->aggregateRootDecorator = $anAggregateRootDecorator;
	}
}
