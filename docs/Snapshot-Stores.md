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

### In Memory Store

Mostly for the use in tests. This store will just store the objects in an array structure in memory.

```php
use Psa\EventSourcing\SnapshotStore\InMemoryStore;

$store = new InMemoryStore();
```

### PSR Cache ItemPool Store

This snapshot store allows you to utilize any PSR cache item pool to be used as a snapshot store.

```php
use Psa\EventSourcing\SnapshotStore\PsrCacheItemPoolStore;

$store = new PsrCacheItemPoolStore(new SomePsrCacheItemPoolImplementation());
```

## Serializers

Serializers are the objects that take care of the serialization of the aggregates. The stores included in this library use by default the `SerializeSerializer`. It's just an OOP wrapper around phps serilaize() and unserialize().

You are free to implement your own serializers by implementing the interface `Psa\EventSourcing\SnapshotStore\Serializer\SerializerInterface`.
