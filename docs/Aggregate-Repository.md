# Aggregate Repository

An aggregate repository will save an aggregates events to the WRITE model. This is in our case the event store, because we do event sourcing.

Technically you can call `save($aggregate)` on the repository as long as the aggregate implements `\Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface`. But it is highly recommended to add a typed save() method to the repository, to ensure type safity when saving: 

```php
use \Psa\EventSourcing\Aggregate\AbstractAggregateRepository;

class FooAggregateRepository extends AbstractAggregateRepository
{
	public function save(FooAggregate $aggregate): void
	{
		$this->saveAggregate($aggregate);
	}
}
```

It doesn't really matter for the library itself where you create the files and what naming schema you use this is within the concern of your application.

Use your favorite way of dependency injection to get an instance of the repository where you need it or construct it manually and then simply call
`save($aggregate)` on it. If something went wrong exceptions are thrown.

## Aggregate Translator

An aggregate translator extracts the events from a domain aggregate to persist them and vice versa. 

## Event Translator

An event translator turns the returned format of the events from the store back into the original event objects expected by the aggregate.

When persisting the state of an aggregate, the events from the aggregate are turned into the format expected by the event store.

The library comes with the `AggregateChangedEventTranslator`, that is used by default. It expects that your domain events implement the `AggregateChangedEventInterface` interface. If your events don't implement it, it will throw execptions when the event is processed. 
