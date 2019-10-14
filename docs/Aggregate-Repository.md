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

Use your favorite way of dependency injection to get an instance of the repository where you need it or construct it manually and then simply call
`save($aggregate)` on it. If something went wrong exceptions are thrown.
 
