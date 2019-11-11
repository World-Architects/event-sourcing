<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use ReflectionClass;

/**
 * Event Translator
 *
 * Converts domain events to whatever the store implementation expects and vice
 * versa.
 */
class EventReflectionTranslator implements EventTranslatorInterface
{
	/**
	 *
	 */
	public function __construct() {
	}

	/**
	 * @param string $aggregateId Aggregat Id
	 * @param \Psa\EventSourcing\Aggregate\AggregateType $aggregateType
	 * @param array $events Events
	 * @return array
	 */
	public function toStore(string $aggregateId, AggregateType $aggregateType, array $events): array
	{
		$storeEvents = [];
		foreach ($events as $event) {
			$reflection = new ReflectionClass($event);
			$payload = [];

			foreach ($reflection->getProperties() as $property) {
				if (!$property->isPublic()) {
					$property->setAccessible(true);
				}
				$payload[$property->getName()] = $property->getValue();
			}

			$eventType = EventType::fromEvent($event);
			$event = $reflection->newInstance();

			$storeEvents[] = new EventData(
				EventId::generate(),
				$eventType->toString(),
				true,
				json_encode($payload),
				[
					'event_class' => get_class($event)
				]
			);
		}

		return $storeEvents;
	}

	/**
	 * @param \Prooph\EventStore\RecordedEvent $recordedEvent Recorded Event
	 * @return object
	 */
	public function fromStore(RecordedEvent $recordedEvent): object
	{
		$metadata = $recordedEvent->metadata();
		$payload = $recordedEvent->data();

		if (!isset($metadata['event_class'])) {
			// exception?
		}

		$reflection = new ReflectionClass($metadata['event_class']);
	}

	/**
	 * @param array $typeMap Type Mapping
	 * @return self
	 */
	public function withTypeMap(array $typeMap): EventTranslatorInterface
	{
		return self;
	}
}
