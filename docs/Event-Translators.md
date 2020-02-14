# Events Translators

An event translator is used by a repository to translate an event object into information the event store can understand. This is basically the transformation of the events from your system to the event data the event store understands and needs.

The library comes with two translators. Reflection and interface based.

## Reflection based Translator

The reflection based translator will extract *all* properties from an event object and turn them into to payload of  the event that gets stored in the event store.

You can pass an array of properties you want to exclude to the constructor of the translator.

```php
$translator = new EventReflectionTranslator([
    'somePropertyToBeExcluded'
]);
```

## Interface based Translator

The interface based reflection translator requires that your event implement the `Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface`. 

```php
$translator = new AggregateChangedEventTranslator();
```

## Implementing your own translators

An event translator must implement the `Psa\EventSourcing\EventStoreIntegration\EventTranslatorInterface`.

What exactly you do in the internals of the methods is totally up to you and depends totally on the way your domain event objects are implemented.

Fore example you could implement your own translator that requries a method on your events like `getPayload()` that will return an array or a concrete type with the payload for the event. It is totally up to you.
