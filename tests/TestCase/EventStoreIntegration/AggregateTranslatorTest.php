<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\EventStoreIntegration;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Account;

/**
 * AggregateTranslatorTest
 */
class AggregateTranslatorTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testTranslator(): void
	{
		$translator = new AggregateTranslator();

		$account = Account::create('test', 'test');
		$id = $account->aggregateId();

		$result = $translator->extractAggregateId($account);
		$this->assertEquals($id, $result);

		$result = $translator->extractAggregateVersion($account);
		$this->assertEquals(1, $result);

		$result = $translator->extractPendingStreamEvents($account);
		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
	}

	/**
	 * @return void
	 */
	public function testSettingAndGettingDecorator(): void
	{
		$object = new \stdClass();
		$translator = new AggregateTranslator();
		$translator->setAggregateRootDecorator($object);
		$result = $translator->getAggregateRootDecorator();
		$this->assertEquals($object, $result);
	}
}
