<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\Projection;

use Prooph\EventStore\EventAppearedOnCatchupSubscription;
use Prooph\EventStore\EventAppearedOnPersistentSubscription;
use Prooph\EventStore\EventStoreCatchUpSubscription;
use Prooph\EventStore\EventStorePersistentSubscription;
use Prooph\EventStore\ResolvedEvent;

/**
 * Stdout Projection
 *
 * Writes a stream to STDOUT, useful for debugging
 */
class StdoutPersistentProjection implements EventAppearedOnPersistentSubscription
{
	use StdoutTrait;

	/**
	 * @inheritDoc
	 */
	public function __invoke(
		EventStorePersistentSubscription $subscription,
		ResolvedEvent $resolvedEvent,
		?int $retryCount = null
	): void {
		$this->writeEventToStdOut($resolvedEvent);
	}
}
