<?php

declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;
use Prooph\EventStore\RecordedEvent;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
use Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface;
use Psa\EventSourcing\Aggregate\Event\EventType;
use RuntimeException;

/**
 * Aggregate Changed Event Translator
 *
 * This translator will only work with events implementing the AggregateChangedEventInterface!
 *
 * If you want to work with an aggregate that provides the events in a different
 * object structure you'll have to implement your own event translator.
 */
class AggregateChangedEventTranslator implements EventTranslatorInterface
{
	/**
	 * @var string
	 */
	protected $factoryMethod = 'occur';

	/**
	 * @var array
	 */
	protected $eventTypeMap = [];

	/**
	 * @var callable|null
	 */
	protected $customHandler;

	/**
	 * @param array $typeMap Map
	 * @return self
	 */
	public static function fromTypeMap(array $typeMap): self
	{
		$self = new self();
		$self->eventTypeMap = $typeMap;

		return $self;
	}

	/**
	 * @param array $typeMap Type Mapping
	 * @return self
	 */
	public function withTypeMap(array $typeMap): EventTranslatorInterface
	{
		$self = clone $this;
		$self->eventTypeMap = $typeMap;

		return $self;
	}

	/**
	 * @param \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 * @param string $aggregateId Aggregate Id
	 * @param string $aggregateType String
	 * @return \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface
	 */
	public function enrichEventMetadata(
		AggregateChangedEventInterface $event,
		string $aggregateId,
		string $aggregateType
	): AggregateChangedEventInterface {
		return $event
			->withAddedMetadata('_aggregate_id', $aggregateId)
			->withAddedMetadata('_aggregate_type', $aggregateType)
			->withAddedMetadata('_aggregate_version', $event->aggregateVersion());
	}

	/**
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
			/**
			 * @var \Psa\EventSourcing\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event
			 */
			if (!$event instanceof AggregateChangedEventInterface) {
				throw new RuntimeException(sprintf(
					'Event %s does not implemt %s',
					(string)$aggregateType,
					AggregateChangedEventInterface::class
				));
			}

			$eventType = EventType::fromEvent($event);
			$event = $this->enrichEventMetadata($event, $aggregateId, $aggregateType->toString());

			$storeEvents[] = new EventData(
				EventId::generate(),
				$eventType->toString(),
				true,
				json_encode($event->payload()),
				json_encode($event->metadata())
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
		$eventType = $recordedEvent->eventType();
		$eventClass = '';

		if ($this->customHandler !== null) {
			if (!is_callable($this->customHandler)) {
				throw new RuntimeException('Custom handler is not a callable');
			}

			$handler = $this->customHandler;
			$eventClass = $handler($eventType, $recordedEvent);
		}

		if (isset($this->eventTypeMap[$eventType])) {
			$eventClass = $this->eventTypeMap[$eventType];
		}

		if (!class_exists($eventClass)) {
			throw new RuntimeException(sprintf(
				'Could not resolve event of type `%s` to class. %s',
				$eventClass,
				$recordedEvent->eventId()
			));
		}

		return $eventClass::{$this->factoryMethod}(
			json_decode($recordedEvent->metadata(), true)['_aggregate_id'],
			json_decode($recordedEvent->data(), true),
			json_decode($recordedEvent->metadata(), true)
		);
	}
}
