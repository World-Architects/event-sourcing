<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate;

use Psa\EventSourcing\EventStoreIntegration\EventTranslator;
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

use Psa\EventSourcing\Aggregate\AggregateType;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\Test\TestApp\Domain\Account;
use Psa\EventSourcing\Test\TestApp\Domain\AccountId;
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
		$eventTranslator = new EventTranslator();

		$account = Account::create(
			'Test',
			'Description'
		);

		$repository = new AccountRepository(
			$eventStore,
			$aggregateTranslator,
			$eventTranslator,
			AggregateType::fromMapping(['Account' => Account::class])
		);

		//dd($repository->getAggregate('ba2c2d45-2a5c-4949-97f7-fd05a14ec980'));
		//return;

		$repository->save($account);

		$accountId = AccountId::fromString($account->aggregateId());

		$account = $repository->get($accountId);

		$account->update('Changed name', 'Changed description');
		$repository->save($account);

		$account = $repository->get($accountId);
		var_dump($account->jsonSerialize());

		for ($x = 1; $x <= 127; $x++) {
			$account->update('Changed name - ' . $x, 'Changed description - ' . $x);
			$repository->save($account);
		}

		sleep(3);

		$account = $repository->get($accountId);
		var_dump($account->jsonSerialize());
	}
}
