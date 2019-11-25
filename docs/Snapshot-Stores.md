# Snapshot Stores

Snapshot stores take a snapshot of the **current state** of an aggregate. The aggregate gets serialized, it's actual version read and stored along the serialized object in the store.

When do you want to take a snapshot? By a rule of thumb you should take snapshots when you have aggregates with a really huge event history. Just for the sake of taking a number, if you have 10k events you might want to start to take a snapshot every thousand events. The numbers are randomly picked, it really depends on the specific case.

## Stores

### PDO Snapshot store

The library comes with a PDO based snapshot store and will store the serialized aggregates in a SQL database.

```php
use Psa\EventSourcing\SnapshotStore\PdoSqlStore;
use PDO;

$store = new PdoSqlStore(new PDO(/*...*/)));
```

You can use either the SQL file [event_Store_snapshots_table.sql](../resources/SQL/event_Store_snapshots_table.sql) provided by this library in the resources folder, or you can come up with your own migration in your app for it. However, you need that table. 

If you want to create your own migration file, here are the fields you must create:
```sql
`aggregate_id` CHAR(36) NOT NULL,
`aggregate_type` VARCHAR(255) NOT NULL,
`aggregate_root` MEDIUMTEXT NOT NULL,
`aggregate_version` INT(11) NOT NULL,
`created_at` DATETIME NOT NULL,
```

### In Memory Store

Mostly for the use in tests. This store will just store the objects in an array structure in memory.

```php
use Psa\EventSourcing\SnapshotStore\InMemoryStore;

$store = new InMemoryStore();
```

## Serializers

Serializers are the objects that take care of the serialization of the aggregates. The stores included in this library use by default the `SerializeSerializer`. It's just an OOP wrapper around phps serilaize() and unserialize().

You are free to implement your own serializers by implementing the interface `Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface`.

Keep in mind that to be able to serialize and restore your object properly, you need to make sure that your object shouldn't have any open resource handlers or connections. You might be able tore restore them, but your aggregate shouldn't have anything like that and if it has you can't be sure to be able to restore them again correctly!

## Using Stores

Event stores accept only an object that implements the `Psa\EventSourcing\SnapshotStore\SnapshotInterface` as argument.

When and were you create a snapshot is mostly up to your implementation and use case. It might be useful to create a snapshot after a specific event appeared or every 100 or 1000 events.

## Example:

```php
$account = Account::create('test', 'test');
$aggregateId = $account->aggregateId();

$snapshot = new Snapshot(
    get_class($account),
    $aggregateId,
    $account,
    $account->aggregateVersion(),
    new DateTimeImmutable()
);

// writing
$this->store->store($snapshot);

// reading
$snapshot = $this->>store->get($aggregateId);

// deleting
$this->store->delete($aggregateId);
```
