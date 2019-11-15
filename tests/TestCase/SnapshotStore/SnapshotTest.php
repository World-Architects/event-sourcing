<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\SnapshotStore;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\SnapshotStore\Snapshot;
use stdClass;

/**
 * Snapshot Test
 */
class SnapshotTest extends TestCase
{
	/**
	 * Testing an invalid aggregate type, it has to be a non empty string
	 *
	 * @return void
	 */
	public function testInvalidArgumentException(): void
	{
		$this->expectException(\Assert\InvalidArgumentException::class);

		$created = new DateTimeImmutable('now');
		$object = new stdClass();

		$snapshot = new Snapshot(
			'',
			'dcb6eef4-d2b8-4f1b-8302-33fdc0ec360b',
			$object,
			1,
			$created
		);
	}

	/**
	 * Testing that the snapshot needs a valid aggregate UUID
	 *
	 * @return void
	 */
	public function testInvalidArgumentException2(): void
	{
		$this->expectException(\Assert\InvalidArgumentException::class);

		$created = new DateTimeImmutable('now');
		$object = new stdClass();

		$snapshot = new Snapshot(
			'Account',
			'must-be-a-uuid',
			$object,
			1,
			$created
		);
	}

	/**
	 * Testing that the snapshot version cant be lower than 1
	 *
	 * @return void
	 */
	public function testInvalidArgumentException3(): void
	{
		$this->expectException(\Assert\InvalidArgumentException::class);

		$created = new DateTimeImmutable('now');
		$object = new stdClass();

		$snapshot = new Snapshot(
			'Account',
			'dcb6eef4-d2b8-4f1b-8302-33fdc0ec360b',
			$object,
			0,
			$created
		);
	}

	/**
	 * testSnapshot
	 *
	 * @return void
	 */
	public function testSnapshot(): void
	{
		$created = new DateTimeImmutable('now');
		$object = new stdClass();

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
