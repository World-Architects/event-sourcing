# Aggregate Translators

An aggregate translator is used by a repository to translate an aggregate object into the information the event store needs.

## Aggregate Reflection Translator

The reflection based translator will attempt to extract certain properties, public, protected or private, it doesn't matter, from your aggregate.

It also has to call at least one static method that will take care of rebuilding the aggregates state from the events that are passed to it. 

The aggregate reflection translators constructor args allow you to define the mapping of the aggregate objectes properties to what the translator expects.

## Aggregate Translator

This is an interface based aggregate translator. Your aggregates must implement the `Psa\EventSourching\Aggregate\EventSourcedAggregateInterface` to be able to work with this translator.

## Implementing your own translators

An aggregate translator must implement the `Psa\EventSourcing\EventStoreIntegration\AggregateTranslatorInterface`.

