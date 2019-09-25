<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\SnapshotStore\Snapshot;

/**
 * Snapshot Test
 */
class SnapshotTest extends TestCase
{
	/**
	 * testAsserts
	 *
	 * @return void
	 */
	public function testAsserts(): void
	{
		$created = new \DateTimeImmutable('now');
		$object = new \stdClass();

		try {
			$snapshot = new Snapshot(
				'',
				'dcb6eef4-d2b8-4f1b-8302-33fdc0ec360b',
				$object,
				1,
				$created
			);

			$this->fail('No exception thrown, aggregate type can`t be empty');
		}catch (\Assert\InvalidArgumentException $e) {
		}

		try {
			$snapshot = new Snapshot(
				'Account',
				'must-be-a-uuid',
				$object,
				1,
				$created
			);

			$this->fail('No exception thrown, aggregate id must be an UUID');
		}catch (\Assert\InvalidArgumentException $e) {
		}

		try {
			$snapshot = new Snapshot(
				'Account',
				'dcb6eef4-d2b8-4f1b-8302-33fdc0ec360b',
				$object,
				0,
				$created
			);

			$this->fail('No exception thrown, last version can`t be lower than 1');
		}catch (\Assert\InvalidArgumentException $e) {
		}
	}

	/**
	 * testSnapshot
	 *
	 * @return void
	 */
	public function testSnapshot(): void
	{
		$created = new \DateTimeImmutable('now');
		$object = new \stdClass();

		$snapshot = new Snapshot(
			'Account',
			'dcb6eef4-d2b8-4f1b-8302-33fdc0ec360b',
			$object,
			1,
			$created
		);

		$this->assertEquals('Account', $snapshot->aggregateType());
		$this->assertEquals('dcb6eef4-d2b8-4f1b-8302-33fdc0ec360b', $snapshot->aggregateId());
		$this->assertEquals(1, $snapshot->lastVersion());
		$this->assertEquals($object, $snapshot->aggregateRoot());
		$this->assertEquals($created, $snapshot->createdAt());
	}
}
