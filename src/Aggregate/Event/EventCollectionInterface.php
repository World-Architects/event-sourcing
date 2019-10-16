<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate\Event;

use Iterator;

/**
 * Event Collection Interface
 */
interface EventCollectionInterface extends Iterator
{
	/**
	 * @param \Psa\EventSourcing\Aggregate\Event\AggregateChangedEventInterface $event Event
	 */
	public function add(AggregateChangedEventInterface $event): void;
}
