<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\RecordedEvent;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
use Psa\EventSourcing\Aggregate\Event\EventType;
use ReflectionClass;
use RuntimeException;

/**
 * Reflection based Event Translator
 *
 * The reflection based translator will read *all* properties of an event object
 * and use them as a payload for the event that gets persisted.
 *
 * It will also take the class name of the event object and add it to the meta
 * data of the persisted event.
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
	public function __construct(array $excludedProperties = [], array $propertyHandlers = [])
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
			$storeEvents[] = $this->buildEventStoreData(
				$aggregateId,
				$aggregateType,
				$event
			);
		}

		return $storeEvents;
	}

	/**
	 * @param string $aggregateId Aggregate Id
	 * @param string $aggregateType Aggregate Type
	 * @param object $event Event Object
	 * @return \Prooph\EventStore\EventData
	 */
	protected function buildEventStoreData($aggregateId, $aggregateType, $event): EventData
	{
		$eventType = array_values(EventType::fromEvent($event)->getMapping())[0];

		return new EventData(
			EventId::generate(),
			$eventType,
			true,
			json_encode($this->buildPayload(
				$aggregateId,
				$aggregateType,
				$event,
				$eventType
			)),
			json_encode($this->buildMetadata(
				$aggregateId,
				$aggregateType,
				$event,
				$eventType
			))
		);
	}

	/**
	 * @param string $aggregateId Aggregate Id
	 * @param AggregateTypeInterface $aggregateType Aggregate Type
	 * @param object $event Event Object
	 * @param string $eventType Event Type
	 */
	protected function buildPayload($aggregateId, $aggregateType, $event, $eventType): array
	{
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

		return $payload;
	}

	/**
	 * @param string $aggregateId Aggregate Id
	 * @param AggregateTypeInterface $aggregateType Aggregate Type
	 * @param object $event Event Object
	 * @param string $eventType Event Type
	 * @return array
	 */
	protected function buildMetadata($aggregateId, $aggregateType, $event, $eventType): array
	{
		return [
			'aggregate_id' => $aggregateId,
			'event_class' => get_class($event),
			'event_type' => $eventType
		];
	}

	/**
	 * @param \Prooph\EventStore\RecordedEvent $recordedEvent Recorded Event
	 * @return object
	 */
	public function fromStore(RecordedEvent $recordedEvent): object
	{
		$version = $recordedEvent->eventNumber();
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

		$reflection = new ReflectionClass($metadata['event_class']);
		$event = $reflection->newInstanceWithoutConstructor();

		foreach ($payload as $key => $value) {
			if (!$reflection->hasProperty($key)) {
				continue;
			}

			$property = $reflection->getProperty($key);
			$property->setAccessible(true);
			$property->setValue($event, $value);
		}

		if ($reflection->hasProperty('aggregateVersion')) {
			$versionProperty = $reflection->getProperty('aggregateVersion');
			$versionProperty->setAccessible(true);
			$versionProperty->setValue($event, $version);
		} else {
			$event->aggregateVersion = $version;
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
