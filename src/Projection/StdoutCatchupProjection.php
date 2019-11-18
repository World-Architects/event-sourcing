<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

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
	 * @var null|\Prooph\EventStore\EventAppearedOnCatchupSubscription
	 */
	protected $subscription;

	/**
	 * @param null|\Prooph\EventStore\EventAppearedOnCatchupSubscription $subscription Subscription
	 */
	public function __construct(?EventAppearedOnCatchupSubscription $subscription = null)
	{
		$this->subscription = $subscription;
	}

	/**
	 * @inheritDoc
	 */
	public function __invoke(
		EventStoreCatchUpSubscription $subscription,
		ResolvedEvent $resolvedEvent
	): void {
		$this->writeEventToStdOut($resolvedEvent);

		if ($this->subscription !== null) {
			$callback = $this->subscription;
			$callback($subscription, $resolvedEvent);
		}
	}
}
