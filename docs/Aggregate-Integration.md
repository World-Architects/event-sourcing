# Aggregate Integration

To make an aggregate event sourced it must implement `\Psa\EventSourcing\Aggregat\EventSourcedAggregateInterface`.

This library provides two traits `Psa\EventSourcing\Aggregate\EventSourcedTrait` and ``Psa\EventSourcing\Aggregate\EventProducerTrait`.

The `EventSourcedTrait` will process the events while the `EventProducerTrait` provides the methods to record the events:

```php
use Psa\EventSourcing\Aggregate\EventSourcedTrait;
use Psa\EventSourcing\Aggregate\EventProducerTrait;
use Psa\EventSourcing\Aggregat\EventSourcedAggregateInterface;

class PersonAggregate implements EventSourcedAggregateInterface
{
	use EventSourcedTrait;
	use EventProducerTrait;

	protected $aggregateId;
	protected $name;

	public function create(string $name)
	{
		$this->recordThat(new Created($name));
	}

	public function aggregateId(): string
	{
		return $this->aggregateId;
	}

	public function whenCreated(Created $event)
	{
		$this->name = $event->name();
	}
}
```

## Domain Events

Event sourced aggregate events **must** implement `Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface`.
