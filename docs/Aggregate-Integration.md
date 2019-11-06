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

## Aggregate Type

The aggregate type is an identifier used by the store to distinguis different aggregates. Keep in mind that when using the ubiquious language of DDD it is easy to end up with duplicates. Think of the two different contexts of *user* account` and a *book keeping* account. Your system is very likely going to have two Account aggregates in different domains.

Depending on what kind of strategy to determine the aggregate type you've choosen there are several ways of providing it:

### via Interface

If the aggregate implements `Psa\EventSourcing\Aggregate\AggregateProviderInterface` the repository will call that method on the aggregate and use the returned type.

### via Constant

If the aggregate provides a constant, by default `AGGREGATE_TYPE`, the repository will use this as type.

### FQCN fallback

If you don't do anything the repository receiving the aggregate will use the FQCN as aggregate type.

## Domain Events

Event sourced aggregate events **must** implement `Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface`.
