<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventSourcing\EventStoreIntegration;

use Psa\EventSourcing\EventSourcing\Aggregate\AggregateChangedEvent;
use Psa\EventSourcing\EventSourcing\Aggregate\AggregateRoot;
use BadMethodCallException;
use Iterator;
use RuntimeException;

/**
 * AggregateRootDecorator
 */
class AggregateRootDecorator extends AggregateRoot
{
    public static function newInstance(): self
    {
        return new static();
    }

    public function extractAggregateVersion(AggregateRoot $anAggregateRoot): int
    {
        return $anAggregateRoot->version;
    }

    /**
     * @param AggregateRoot $anAggregateRoot
     *
     * @return \Psa\EventSourcing\EventSourcing\AggregateChangedEvent[]
     */
    public function extractRecordedEvents(AggregateRoot $anAggregateRoot): array
    {
        return $anAggregateRoot->popRecordedEvents();
    }

    public function extractAggregateId(AggregateRoot $anAggregateRoot): string
    {
        return $anAggregateRoot->aggregateId();
    }

    /**
     * @throws RuntimeException
     */
    public function fromHistory($arClass, Iterator $aggregateChangedEvents): AggregateRoot
    {
        if (!class_exists($arClass)) {
            throw new RuntimeException(
                sprintf('Aggregate root class %s cannot be found', $arClass)
            );
        }

        return $arClass::reconstituteFromHistory($aggregateChangedEvents);
    }

    public function replayStreamEvents(AggregateRoot $aggregateRoot, Iterator $events): void
    {
        $aggregateRoot->replay($events);
    }

    /**
     * @throws BadMethodCallException
     */
    public function aggregateId(): string
    {
        throw new BadMethodCallException('The AggregateRootDecorator does not have an id');
    }
}
