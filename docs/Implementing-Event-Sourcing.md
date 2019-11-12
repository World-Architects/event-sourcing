# Implementing Event Sourcing

## Event Sourcing Basics

The basic idea of event sourcing is that an object that maintains state of a business process records its state changes as a series of events. These events are executed and their name or type and their data is persistet in a storage system, usally called an event store.

The library features two ways of implementing that:

 * None intrusive reflection based
 * Interface based 

It should be relatively easy to implement any of both. For an existing implementation is very likely better to go for the reflection based implementation.

## None intrusive dependency free implementation

This way of implementing our library will introduce no dependencies to your domain code but relies on reflections to extract the required information for the event store from your code. 

However, your objects must at least provide some properties and / or methods that can be used to extract the required data for the event store from them.

The reflection based aggregate translator and the reflection based event translator both allow you to define what methods and properties are extracted.

Example pseudo code:

```php
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;

$repository = new AccountRepository(
	$eventStore,
	new AggregateReflectionTranslator(),
	new EventReflectionTranslator()
);
```

## Interface based implementation

The interface based implementation requires you that your aggregate and events implement interfaces.

Your aggregate must implement `Psa\EventSourcing\Aggregate\EventSourcedAggregateInterface`

And your events must implement `Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface`

```php
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\EventStoreIntegration\AggregateChangedEventTranslator;

$repository = new AccountRepository(
	$eventStore,
	new AggregateTranslator()
	new AggregateChangedEventTranslator(),
);
```
