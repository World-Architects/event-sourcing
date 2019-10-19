<?php
declare(strict_types=1);

namespace Psa\EventSourcing\EventStoreIntegration;

use Prooph\EventStore\EventData;
use Prooph\EventStore\RecordedEvent;
use RuntimeException;

/**
 * AggregateRootDecorator
 */
class EventTranslator
{
	/**
	 * @var string
	 */
	protected $factoryMethod = 'occur';

	/**
	 * @var array
	 */
	protected $eventTypeClassMap = [];

	/**
	 * @var callable|null
	 */
	protected $customHandler;

	/**
	 * @param array $map Map
	 * @return self
	 */
	public static function fromTypeMap(array $map): self
	{
		$self = new self();
		$self->eventTypeClassMap = $map;

		return $self;
	}

	/**
	 * @param \Prooph\EventStore\RecordedEvent $recordedEvent Recorded Event
	 * @return object
	 */
	public function resolveEvent(RecordedEvent $recordedEvent)
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

		if (isset($this->eventTypeClassMap[$eventType])) {
			$eventClass = $this->eventTypeClassMap[$eventType];
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
