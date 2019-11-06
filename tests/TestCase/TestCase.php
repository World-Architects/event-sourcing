<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase;

use PDO;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Prooph\EventStore\EndPoint;
use Prooph\EventStoreClient\EventStoreConnectionFactory;

/**
 * Test Case
 */
class TestCase extends PhpUnitTestCase
{
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
