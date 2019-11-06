<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event;

use Assert\Assert;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;

/**
 * Aggregate Changed Event
 */
class AggregateChangedEvent implements AggregateChangedEventInterface
{
	/**
	 * @var \Ramsey\Uuid\UuidInterface
	 */
	protected $uuid;

	/**
	 * @var array
	 */
	protected $payload = [];

	/**
	 * @var \DateTimeImmutable
	 */
	protected $createdAt;

	/**
	 * @var array
	 */
	protected $metadata = [];

	/**
	 * @inheritDoc
	 */
	public static function occur(string $aggregateId, array $payload = [], array $metadata = []): AggregateChangedEventInterface
	{
		Assert::that($aggregateId)->uuid();

		return new static($aggregateId, $payload, $metadata);
	}

	/**
	 * Constructor
	 *
	 * @param string $aggregateId Aggregate UUID
	 * @param array $payload Payload
	 * @param array $metadata Metadata
	 */
	protected function __construct(string $aggregateId, array $payload, array $metadata = [])
	{
		Assert::that($aggregateId)->uuid();

		//metadata needs to be set before setAggregateId and setVersion is called
		$this->metadata = $metadata;
		$this->setAggregateId($aggregateId);
		$this->setAggregateVersion($metadata['_aggregate_version'] ?? 1);
		$this->setPayload($payload);
		$this->init();
	}

	/**
	 * Initializes the event
	 *
	 * @return void
	 */
	protected function init(): void
	{
		if ($this->uuid === null) {
			$this->uuid = Uuid::uuid4();
		}

		if ($this->createdAt === null) {
			$this->createdAt = new DateTimeImmutable(
				'now',
				new DateTimeZone('UTC')
			);
		}
	}

	/**
	 * Gets the meta data
	 *
	 * @return array
	 */
	public function metadata(): array
	{
		return $this->metadata;
	}

	/**
	 * Gets the aggregate id
	 *
	 * @return string
	 */
	public function aggregateId(): string
	{
		return $this->metadata['_aggregate_id'];
	}

	/**
	 * Return message payload as array
	 *
	 * The payload should only contain scalar types and sub arrays.
	 * The payload is normally passed to json_encode to persist the message or
	 * push it into a message queue.
	 */
	public function payload(): array
	{
		return $this->payload;
	}

	/**
	 * With    public function version(): int
		 {
			 return $this->metadata['_aggregate_version'];
		 }
		 public function withVersion(int $version): AggregateChanged
		 {
			 $self = clone $this;
			 $self->setVersion($version);
			 return $self;
		 } meta data
	 *
	 * @return self
	 */
	public function withMetadata(array $metadata): AggregateChangedEventInterface
	{
		$event = clone $this;
		$event->metadata = $metadata;

		return $event;
	}

	/**
	 * Returns new instance of message with $key => $value added to metadata
	 *
	 * Given value must have a scalar or array type.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return self
	 */
	public function withAddedMetadata(string $key, $value): AggregateChangedEventInterface
	{
		$event = clone $this;
		$event->metadata[$key] = $value;

		return $event;
	}

	/**
	 * Gets the version
	 *
	 * @return int
	 */
	public function aggregateVersion(): int
	{
		return (int)$this->metadata['_aggregate_version'];
	}

	/**
	 * With version
	 *
	 * @param int $version Version
	 * @return \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface
	 */
	public function withAggregateVersion(int $version): AggregateChangedEventInterface
	{
		$self = clone $this;
		$self->setAggregateVersion($version);

		return $self;
	}

	/**
	 * Sets the aggregate id
	 *
	 * @param string $aggregateId Aggregate UUID
	 * @return void
	 */
	protected function setAggregateId(string $aggregateId): void
	{
		Assert::that($aggregateId)->uuid($aggregateId);

		$this->metadata['_aggregate_id'] = $aggregateId;
	}

	/**
	 * Sets the version
	 *
	 * @param int $version Version
	 * @return void
	 */
	protected function setAggregateVersion(int $version): void
	{
		$this->metadata['_aggregate_version'] = $version;
	}

	/**
	 * This method is called when message is instantiated named constructor fromArray
	 *
	 * @param array $payload Payload
	 * @return void
	 */
	protected function setPayload(array $payload): void
	{
		$this->payload = $payload;
	}

	/**
	 * Gets a value from the payload
	 *
	 * @param string $property Property
	 * @return mixed
	 */
	protected function getFromPayload(string $property)
	{
		if (!isset($this->{$property})
			|| $this->{$property} === null
			&& isset($this->payload[$property])
		) {
			$this->{$property} = $this->payload[$property];

			return $this->payload[$property];
		}

		return $this->{$property};
	}

	/**
	 * Magic Call
	 *
	 * @param string $name Name
	 * @param array $arguments Arguments
	 */
	public function __call($name, $arguments)
	{
		return $this->getFromPayload($name);
	}
}
