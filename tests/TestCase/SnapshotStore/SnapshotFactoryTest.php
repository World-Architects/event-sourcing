<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\SnapshotStore\Snapshot;
use Psa\EventSourcing\SnapshotStore\SnapshotFactory;
use Psa\EventSourcing\SnapshotStore\SnapshotInterface;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Account;

/**
 * Snapshot Factory Test
 */
class SnapshotFactoryTest extends TestCase
{
	/**
	 * testFromEventSourcedAggregate
	 *
	 * @return void
	 */
	public function testFromEventSourcedAggregate(): void
	{
		$aggregate = Account::create('test', 'test');
		$accountId = $aggregate->aggregateId();

		$result = SnapshotFactory::fromEventSourcedAggregate($aggregate);
		$this->assertInstanceOf(SnapshotInterface::class, $result);
		$this->assertEquals($accountId, $result->aggregateId());
	}
}
