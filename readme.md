# Event Sourcing

#### Master
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/quality-score.png?b=master&s=3137e02a67d7a4d7f6cdb76ae9be0e1c49aa7b30)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/coverage.png?b=master&s=2cdf93f9ee0dbd6dd390cd6948e25702068f5bf5)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/build.png?b=master&s=c164216fdf37936733034610d68514350f34d792)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=master)

#### Develop
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/quality-score.png?b=develop&s=3137e02a67d7a4d7f6cdb76ae9be0e1c49aa7b30)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/coverage.png?b=develop&s=2cdf93f9ee0dbd6dd390cd6948e25702068f5bf5)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=develop)
[![Build Status](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/build.png?b=develop&s=c164216fdf37936733034610d68514350f34d792)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=develop)

This library provides tools for an [event sourcing](https://martinfowler.com/eaaDev/EventSourcing.html) implementation.

The event store system that this library is using is https://eventstore.org/ and using the [Prooph](https://github.com/prooph) client libraries ([Async](https://github.com/prooph/event-store-client), [HTTP](https://github.com/prooph/event-store-http-client)) to communicate with it. 

## Installation

Add it via composer to your project

```sh
composer require psa/event-sourcing
```

## Documentation

Please see the [docs folder](./docs/index.md) for the library documentation.

You can find the event store documentation here https://eventstore.org/docs/.

## Composer Commands

* **csfix** - Runs phpcbf and fixes coding standard problems
* **cscheck** - Checks the coding standard
* **analyze** - Runs the static code analyzer
* **test** - Runs phpunit

## Copyright

Copyright 2019 PSA Ltd. All rights reserved.
