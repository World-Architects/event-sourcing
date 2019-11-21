<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Projection\Async;

use Amp\Success;
use Prooph\EventStore\Async\CatchUpSubscriptionDropped;
use Prooph\EventStore\Async\EventStoreCatchUpSubscription;
use Prooph\EventStore\SubscriptionDropReason;
use Throwable;

/**
 * StdoutCatchUpSubscriptionDropped
 */
class StdoutCatchUpSubscriptionDropped implements CatchUpSubscriptionDropped
{

	/**
	 * @var \Prooph\EventStore\Async\CatchUpSubscriptionDropped
	 */
	protected $subscriptionDropped;

	/**
	 * @param null|\Prooph\EventStore\Async\CatchUpSubscriptionDropped
	 */
	public function __construct(
		?CatchUpSubscriptionDropped $subscriptionDropped = null
	) {
		$this->subscriptionDropped = $subscriptionDropped;
	}

	/**
	 * @param Prooph\EventStore\Async\CatchUpSubscriptionDropped Suscription
	 * @param Prooph\EventStore\Async\EventStoreCatchUpSubscription Reason
	 * @param SubscriptionDropReason Throwable
	 * @return void
	 */
	public function __invoke(
		EventStoreCatchUpSubscription $subscription,
		SubscriptionDropReason $reason,
		Throwable $exception = null
	): void {
		fwrite(STDOUT, sprintf(
			'Subscription `%s` for stream `%s` dropped.' . PHP_EOL,
			$subscription->subscriptionName(),
			$subscription->streamId()
		));
		fwrite(STDOUT, 'Reason: ' . $reason->name() . PHP_EOL);

		if ($this->subscriptionDropped) {
			$callable = $this->subscriptionDropped;

			$callable();
		}
	}
}
