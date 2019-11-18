<?php

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Iterator;
use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\RecordedEvent;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Event\EventType;
use Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface;
use ReflectionClass;
use RuntimeException;

/**
 * Reflection based Event Translator
 *
 * The reflection based translator will read *all* properties of an event object
 * and use them as a payload for the event that gets persistet.
 *
 * It will also take the class name of the event object and add it to the meta
 * data of the persistet event.
 */
class EventReflectionTranslator implements EventTranslatorInterface
{
	/**
	 * @var array
	 */
	protected $excludedProperties = [];

	/**
	 * Constructor
	 *
	 * @param array $excludedProperties Exclude this properties from conversion
	 */
	public function __construct(array $excludedProperties = [])
	{
		$this->excludedProperties = $excludedProperties;
	}

	/**
	 * @param string $aggregateId Aggregat Id
	 * @param \Psa\EventSourcing\Aggregate\AggregateType $aggregateType
	 * @param array $events Events
	 * @return array
	 */
	public function toStore(
		string $aggregateId,
		AggregateTypeInterface $aggregateType,
		array $events
	): array {
		$storeEvents = [];
		foreach ($events as $event) {
			$reflection = new ReflectionClass($event);
			$payload = [];

			foreach ($reflection->getProperties() as $property) {
				if (in_array($property->getName(), $this->excludedProperties)) {
					continue;
				}

				if (!$property->isPublic()) {
					$property->setAccessible(true);
				}

				$payload[$property->getName()] = $property->getValue($event);
			}

			$eventType = EventType::fromEvent($event);

			$storeEvents[] = new EventData(
				EventId::generate(),
				$eventType->toString(),
				true,
				json_encode($payload),
				json_encode($this->buildMetadata(
					$aggregateId,
					$aggregateType,
					$event,
					$eventType
				))
			);
		}

		return $storeEvents;
	}

	/**
	 * @return array
	 */
	protected function buildMetadata($aggregateId, $aggregateType, $event, $eventType): array
	{
		return [
			'aggregate_id' => $aggregateId,
			'event_class' => get_class($event)
		];
	}

	/**
	 * @param \Prooph\EventStore\RecordedEvent $recordedEvent Recorded Event
	 * @return object
	 */
	public function fromStore(RecordedEvent $recordedEvent): object
	{
		$metadata = json_decode($recordedEvent->metadata(), true);
		$payload = $recordedEvent->data();
		if ($recordedEvent->isJson()) {
			$payload = json_decode($payload, true);
		}

		if (!isset($metadata['event_class'])) {
			throw new RuntimeException(sprintf(
				'Key `event_class` is missing in metadata array'
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
			$property->setValue($event, $value);
		}

		return $event;
	}

	/**
	 * @param array $typeMap Type Mapping
	 * @return self
	 */
	public function withTypeMap(array $typeMap): EventTranslatorInterface
	{
		return $this;
	}
}
