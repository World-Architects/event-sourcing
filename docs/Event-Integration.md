# Event Integration

The events your business logic or domain model is going to produce **muts** implement the `\Psa\EventSourcing\Aggregate\Event\\AggregateChangedEventInterface`. The interfaces ensures that your event is compatible with the event store system and the library can read and process it.

If you don't want to implement all of that on your own you can extend the provided `\Psa\EventSourcing\Aggregate\Event\\AggregateChangedEvent` class. 

```php
<?php
declare(strict_types=1);

namespace App\Domain\Accounting\Event;

use Psa\EventSourcing\Aggregate\Event\AggregateChangedEvent;

class AccountCreated extends AggregateChangedEvent
{
}
```
