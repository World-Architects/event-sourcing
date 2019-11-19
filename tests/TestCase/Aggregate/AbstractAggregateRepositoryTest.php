<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\UserCredentials;
use Prooph\EventStore\Transport\Http\EndpointExtensions;
use Prooph\EventStoreHttpClient\ConnectionSettings;
use Prooph\EventStoreHttpClient\EventStoreConnectionFactory;
use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\EventStoreIntegration\AggregateChangedEventTranslator;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Account;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\AccountId;
use Psa\EventSourcing\Test\TestApp\Infrastructure\Repository\AccountRepository;

use function Clue\StreamFilter\fun;

/**
 * Abstract Aggregate Repository Test
 */
class AbstractAggregateRepositoryTest extends TestCase
{
	/**
	 *@return void
	 */
	public function testAbstractRepository(): void
	{
		//$this->markTestSkipped();

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

		$account = Account::create(
			'Test',
			'Description'
		);
		$accountId = AccountId::fromString($account->aggregateId());

		$aggregateTranslator = new AggregateTranslator();
		$eventTranslator = new AggregateChangedEventTranslator();

		$repository = new AccountRepository(
			$eventStore,
			$aggregateTranslator,
			$eventTranslator,
			null
		);

		$repository->save($account);
		$account = $repository->get($accountId);

		$this->assertEquals($account->aggregateId(), (string)$accountId);

		$account->update('Changed name', 'Changed description');
		$repository->save($account);

		$account = $repository->get($accountId);
		//var_dump($account->jsonSerialize());

		for ($x = 1; $x <= 127; $x++) {
			$account->update('Changed name - ' . $x, 'Changed description - ' . $x);
			$repository->save($account);
		}

		//sleep(3);

		$account = $repository->get($accountId);
		//var_dump($account->jsonSerialize());
	}
}
