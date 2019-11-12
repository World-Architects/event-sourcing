<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\RecordedEvent;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use ReflectionClass;
use RuntimeException;

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

				$payload[$property->getName()] = $property->getValue($event);
			}

			$eventType = EventType::fromEvent($event);
			$event = $reflection->newInstance();

			$storeEvents[] = new EventData(
				EventId::generate(),
				$eventType->toString(),
				true,
				json_encode($payload),
				json_encode([
					'event_class' => get_class($event)
				])
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
			throw new RuntimeException(sprintf(
				'Event class is missing from metadata'
			));
		}

		$event = new $metadata['event_class']();
		$reflection = new ReflectionClass($event);

		foreach ($payload as $key => $value) {
			if (!$reflection->hasProperty($key)) {
				continue;
			}

			$property = $reflection->getProperty($key);
			$property->setAccessible(true);
			$property->setValue($property);
		}

		return $event;
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
