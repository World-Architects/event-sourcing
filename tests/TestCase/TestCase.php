<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client;
use PDO;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\UserCredentials;
use Prooph\EventStore\Transport\Http\EndpointExtensions;
use Prooph\EventStoreHttpClient\ConnectionSettings;
use Prooph\EventStoreHttpClient\EventStoreConnectionFactory;

/**
 * Test Case
 */
class TestCase extends PhpUnitTestCase
{
	/**
	 * @var \Prooph\EventStore\EventStoreConnection
	 */
	protected $eventstore;

	/**
	 * @var array
	 */
	protected $sqlFixtures = [];

	/**
	 * @var \PDO
	 */
	protected $pdoTestConnection;

	/**
	 * @return \PDO
	 */
	protected function pdo(): PDO
	{
		if ($this->pdoTestConnection instanceof PDO) {
			return $this->pdoTestConnection;
		}

		$data = [
			'username' => getenv('PDO_TEST_USER', 'root'),
			'password' => getenv('PDO_TEST_PASS', ''),
			'hostname' => getenv('PDO_TEST_HOST', 'localhost'),
			'dbname' => getenv('PDO_TEST_DB', 'test'),
		];

		$this->pdoTestConnection = new PDO(
			'mysql:host=' . $data['hostname'] . ';dbname=' . $data['dbname'],
			$data['username'],
			$data['password']
		);

		return $this->pdoTestConnection;
	}

	/**
	 * @return void
	 */
	public function loadFixtures(): void
	{
		foreach ($this->sqlFixtures as $fixtureFile) {
			$ds = DIRECTORY_SEPARATOR;
			$fixtureFile = __DIR__ . $ds . '..' . $ds . 'Fixtures' . $ds . $fixtureFile . '.sql';
			$sql = file_get_contents($fixtureFile);
			$this->pdo()->exec($sql);
		}
	}

	/**
	 * @return \Prooph\EventStoreHttpClient\EventStoreConnectionFactory
	 */
	public function eventstore()
	{
		if ($this->eventstore !== null) {
			return $this->eventstore;
		}

		$httpClient = new Client(new GuzzleClient());
		$userCredentials = new UserCredentials('admin', 'changeit');

		$this->eventstore = EventStoreConnectionFactory::create(
			new ConnectionSettings(
				new EndPoint(
					getenv('EVENTSTORE_HOST', '127.0.0.1'),
					(int)getenv('EVENTSTORE_PORT', 2113)
				),
				EndpointExtensions::HTTP_SCHEMA,
				$userCredentials
			),
			$httpClient
		);

		return $this->eventstore;
	}

	/**
	 * @inheritDoc
	 */
	public function setUp(): void
	{
		parent::setUp();

		if (!empty($this->sqlFixtures)) {
			$this->loadFixtures();
		}
	}
}

function getenv($name, $default = null)
{
	$value = \getenv($name);
	if ($value === false) {
		return $default;
	}

	return $value;
}

function dd($data)
{
	var_dump($data);
	die();
}
