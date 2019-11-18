<?php

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Assert\Assert;
use Iterator;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
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
	 * @var object
	 */
	protected $aggregateRootDecorator;

	/**
	 * @param object $anAggregateRoot Aggregate object
	 * @return int
	 */
	public function extractAggregateVersion(object $anAggregateRoot): int
	{
		return $this->getAggregateRootDecorator()
			->extractAggregateVersion($anAggregateRoot);
	}

	/**
	 * @param object $anAggregateRoot Aggregate object
	 * @return string
	 */
	public function extractAggregateId(object $anAggregateRoot): string
	{
		$aggreagteId = $this->getAggregateRootDecorator()
			->extractAggregateId($anAggregateRoot);

		Assert::that($aggreagteId)->uuid();

		return $aggreagteId;
	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\AggregateType $aggregateType Aggregate Type
	 * @param \Iterator $historyEvents
	 * @return object reconstructed AggregateRoot
	 */
	public function reconstituteAggregateFromHistory(
		AggregateTypeInterface $aggregateType,
		Iterator $historyEvents
	) {
		if (!$aggregateRootClass = $aggregateType->mappedClass()) {
			$aggregateRootClass = $aggregateType->toString();
		}

		return $this->getAggregateRootDecorator()
			->fromHistory($aggregateRootClass, $historyEvents);
	}

	/**
	 * @param object $anAggregateRoot
	 *
	 * @return array
	 */
	public function extractPendingStreamEvents(object $anAggregateRoot): array
	{
		return $this->getAggregateRootDecorator()
			->extractRecordedEvents($anAggregateRoot);
	}

	/**
	 * @param object $anAggregateRoot
	 * @param Iterator $events
	 * @return void
	 */
	public function replayStreamEvents(object $anAggregateRoot, Iterator $events): void
	{
		$this->getAggregateRootDecorator()
			->replayStreamEvents($anAggregateRoot, $events);
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
	public function setAggregateRootDecorator(object $anAggregateRootDecorator): void
	{
		$this->aggregateRootDecorator = $anAggregateRootDecorator;
	}
}
