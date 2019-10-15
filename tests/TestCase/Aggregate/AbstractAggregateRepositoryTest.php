<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use function Clue\StreamFilter\fun;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\UserCredentials;
//use Prooph\EventStoreClient\ConnectionSettings;
//use Prooph\EventStoreClient\EventStoreConnectionFactory;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client;

use Prooph\EventStore\Transport\Http\EndpointExtensions;
use Prooph\EventStoreHttpClient\ConnectionSettings;
use Prooph\EventStoreHttpClient\EventStoreConnectionFactory;

use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\Test\TestApp\Domain\Account;
use Psa\EventSourcing\Test\TestApp\Domain\AccountRepository;

/**
 * Abstract Aggregate Repository Test
 */
class AbstractAggregateRepositoryTest extends TestCase
{
	/**
	 *@return void
	 */
	public function testAccountRepository(): void
	{
		$httpClient = new Client(new GuzzleClient());
		$userCredentials = new UserCredentials('admin', 'changeit');

		$eventStore = EventStoreConnectionFactory::create(
			new ConnectionSettings(
				new EndPoint('127.0.0.1', 2113),
				EndpointExtensions::HTTP_SCHEMA,
				$userCredentials
			),
			$httpClient
		);

		$aggregateTranslator = new AggregateTranslator();

		$account = Account::create(
			'Test',
			'Description'
		);

		$repository = new AccountRepository(
			$eventStore,
			$aggregateTranslator
		);

		$repository->save($account);
	}

	/**
	 * @return void
	 */
	public function testAsyncAccountRepository(): void
	{
		/*
		$userCredentials = new UserCredentials(
			'admin',
			'changeit'
		);

		$settings = ConnectionSettings::default()
			->withDefaultCredentials($userCredentials);

		$eventStore = EventStoreConnectionFactory::createFromEndPoint(
			new EndPoint('localhost', 1113),
			$settings
		);

		$promise = $eventStore->connectAsync();
		$promise->onResolve(function($error, $value) {
			$account = Account::create(
				'Test',
				'Description'
			);

			$repository = new AccountRepository(
				$eventStore
			);

			$repository->save($account);
		});
		*/
	}
}
