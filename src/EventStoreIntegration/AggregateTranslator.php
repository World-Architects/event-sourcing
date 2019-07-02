<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventSourcing\EventStoreIntegration;

use Iterator;

/**
 * Aggregate Translator
 *
 * Converts domain events to whatever the store implementation expects and vice
 * versa.
 */
final class AggregateTranslator
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
        return $this->getAggregateRootDecorator()->extractAggregateVersion($eventSourcedAggregateRoot);
    }

    /**
     * @param object $anEventSourcedAggregateRoot
     *
     * @return string
     */
    public function extractAggregateId($anEventSourcedAggregateRoot): string
    {
        return $this->getAggregateRootDecorator()->extractAggregateId($anEventSourcedAggregateRoot);
    }

    /**
     * @param AggregateType $aggregateType
     * @param Iterator $historyEvents
     *
     * @return object reconstructed AggregateRoot
     */
    public function reconstituteAggregateFromHistory(string $aggregateType, Iterator $historyEvents)
    {
        if (! $aggregateRootClass = $aggregateType->mappedClass()) {
            $aggregateRootClass = $aggregateType->toString();
        }

        return $this->getAggregateRootDecorator()
            ->fromHistory($aggregateRootClass, $historyEvents);
    }

    /**
     * @param object $anEventSourcedAggregateRoot
     *
     * @return Message[]
     */
    public function extractPendingStreamEvents($anEventSourcedAggregateRoot): array
    {
        return $this->getAggregateRootDecorator()->extractRecordedEvents($anEventSourcedAggregateRoot);
    }

    /**
     * @param object $anEventSourcedAggregateRoot
     * @param Iterator $events
     * @return void
     */
    public function replayStreamEvents($anEventSourcedAggregateRoot, Iterator $events): void
    {
        $this->getAggregateRootDecorator()->replayStreamEvents($anEventSourcedAggregateRoot, $events);
    }

    public function getAggregateRootDecorator(): AggregateRootDecorator
    {
        if (null === $this->aggregateRootDecorator) {
            $this->aggregateRootDecorator = AggregateRootDecorator::newInstance();
        }

        return $this->aggregateRootDecorator;
    }

    public function setAggregateRootDecorator(AggregateRootDecorator $anAggregateRootDecorator): void
    {
        $this->aggregateRootDecorator = $anAggregateRootDecorator;
    }
}
