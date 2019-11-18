# Projections

Projections are simple enough to use with the functionality provided by the Prooph Async and HTTP client.

Because there is nothing we feel that needs abstraction our recommendation is just use what the Prooph library offers you for your projections.

However, this library provides you with a persistent and catchup subscription that will print the events to [STDOUT](https://www.php.net/manual/en/features.commandline.io-streams.php).

You can use them directly or as a decorator by passing another subscription to them that is called after the event was printed to STDOUT.
