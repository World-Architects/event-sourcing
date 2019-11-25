# Aggregate Integration

## Integration via Reflection

You aggregate doesn't have to implement any interface or inherit a specific class, but it has to fulfil a few event sourcing requirements. The reflection based aggregate translator of this library acts like a mapper for properties and methods.

A lot of methods and properties will be protected in your aggregates even a few related to event sourcing. The
reflection implementation can still call them. You can change the properties and method names if you like to, but you'll then have to pass the mapping to the reflection translators constructor to make it fetch the right things from the aggregate.

```php
class PersonAggregate
{
	protected $storedEvents = [];
	protected $aggregateId;
	protected $aggregateVersion = 0;

	/**
	 * @param \Iterator $events Events
	 * @return self
	 */
	public static function reconstituteFromHistory(Iterator $events): self
	{
		$instance = new static();
		$instance->replayEvents($events);

		return $instance;
	}

	/**
	 * Replays a list of events on this aggregate
	 *
	 * It is protected for the reason of not allowing the "public" to access this
	 * method from the outside. Our event store implementation will take care
	 * of that by using reflections to call the method anyway when we need to
	 * add events.
	 *
	 * @param \Iterator $events Events
	 * @return void
	 */
	protected function replayEvents(Iterator $events): void
	{
		foreach ($events as $event) {
			// Do something to re-apply them, this is totally up to your implementation
		}
	}
}
```

If you want an easy way to get started you can as well simply use the dependency free `\Psa\EventSourcing\Aggregate\AggregateTrait` to get started. You can also copy and paste the content of the file and adept it
to your needs in your application.

## Integration via Interface

To make an aggregate event sourced using the interface implementation it must implement `\Psa\EventSourcing\Aggregat\EventSourcedAggregateInterface`.

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

Also if you're not using the classname of the aggregate as type, you'll have to be able to map it. The way the library solves this is that you define the type as array, where the key is type string and the value the class:

```php
['User.Account' => Account::class];
```

Depending on what kind of strategy to determine the aggregate type you've choosen there are several ways of providing it. The aggregate translators
provided by this library will be smart enough to extract the correct type automatically for you.

### via Interface

If the aggregate implements `Psa\EventSourcing\Aggregate\AggregateTypeProviderInterface` the repository will call that method on the aggregate and use the returned type.

```php
namespace App\Domain\Accounting;

use Psa\EventSourcing\Aggregate\AggregateTypeInterface;
use Psa\EventSourcing\Aggregate\AggregateTypeProviderInterface;
use Psa\EventSourcing\Aggregate\AggregateType;

class Account implements AggregateTypeProviderInterface
{ 
    public function aggregateType(): AggregateTypeInterface
    { 
        return AggregateType::fromMapping([
            'Accounting.Account' => Account::class
        ]);
    }
}
```

### via Constant

If the aggregate provides a constant, by default `AGGREGATE_TYPE`, the repository will use this as type.

```php
namespace App\Domain\Accounting;

class Account implements AggregateTypeProviderInterface
{ 
    public const AGGREGATE_TYPE = [
        'Accounting.Account' => Account::class
    ];
}
```

### FQCN fallback

If you don't do anything the repository receiving the aggregate will use the FQCN as aggregate type. In this case the name and class mapped to it will be the FQCN of aggregate.
