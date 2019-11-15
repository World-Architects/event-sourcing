<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Projection;

use Prooph\EventStore\EventAppearedOnCatchupSubscription;
use Prooph\EventStore\EventStoreCatchUpSubscription;
use Prooph\EventStore\ResolvedEvent;

/**
 * Stdout Projection
 *
 * Writes a stream to STDOUT, useful for debugging
 */
class StdoutCatchupProjection implements EventAppearedOnCatchupSubscription
{
	use StdoutTrait;

	/**
	 * @inheritDoc
	 */
	public function __invoke(
		EventStoreCatchUpSubscription $subscription,
		ResolvedEvent $resolvedEvent
	): void {
		$this->writeEventToStdOut($resolvedEvent);
	}
}
