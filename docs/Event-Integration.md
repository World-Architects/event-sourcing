# Event Integration

The events your business logic or domain model is going to produce **must** implement the `\Psa\EventSourcing\Aggregate\Event\\AggregateChangedEventInterface`. The interfaces ensures that your event is compatible with the event store system and the library can read and process it.

If you don't want to implement all of that on your own you can extend the provided `\Psa\EventSourcing\Aggregate\Event\\AggregateChangedEvent` class. 

## Integration via Interface

```php
<?php
namespace App\Domain\Accounting\Event;

use Psa\EventSourcing\Aggregate\Event\AggregateChangedEvent;

class AccountCreated extends AggregateChangedEvent
{
}
```

## Integration via Reflection

You have nothing to do for the reflection based event translator. The only thing you need to keep in mind is, that it will turn *all* properties, no matter if public or protected or private, into the payload that gets written to the event store.

So assuming you have an event object like this, pseudo code:
```php
<?php
namespace App\Domain\Accounting\Event;

class AccountCreated
{
    protected $accountNumber = 1234;
    protected $balance = 10;
}
```

The data passed to the event store will be an array:

```php
[
    'accountNumber' => 1234,
    'balance' => 10
]
```
