<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\Aggregate\Event;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\Aggregate\Event\EventCollection;

/**
 * Event Collection Test
 */
class EventCollectionTest extends TestCase
{
	/**
	 * testCollection
	 *
	 * @return void
	 */
	public function testCollection(): void
	{
		$collection = new EventCollection();
	}
}
