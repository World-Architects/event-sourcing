# Event Sourcing

#### Master
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/quality-score.png?b=master&s=f2ff1c59bed4eae0fd4795da471bcb38a06cb453)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/coverage.png?b=master&s=de38bdfbf8e6c052af203814564c5e19447d7e8c)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/build.png?b=master&s=becdf285e3dd06c23fef5911157c946349c893d8)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/build-status/master)

#### Develop
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/quality-score.png?b=develop&s=f2ff1c59bed4eae0fd4795da471bcb38a06cb453)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/coverage.png?b=develop&s=de38bdfbf8e6c052af203814564c5e19447d7e8c)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/?branch=develop)
[![Build Status](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/badges/build.png?b=develop&s=becdf285e3dd06c23fef5911157c946349c893d8)](https://scrutinizer-ci.com/g/World-Architects/event-sourcing/build-status/develop)

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
