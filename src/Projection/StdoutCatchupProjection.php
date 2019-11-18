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
	 * @var \Prooph\EventStore\EventAppearedOnPersistentSubscription
	 */
	protected $subscription;

	/**
	 * @param|null \Prooph\EventStore\EventAppearedOnPersistentSubscription $subscription Subscription
	 */
	public function __construct(?EventAppearedOnPersistentSubscription $subscription = null) {
		$this->subscription;
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
			$callback($subscription, $resolvedEvent, $retryCount);
		}
	}
}
