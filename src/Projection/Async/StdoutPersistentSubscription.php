<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Projection\Async;

use Amp\Promise;
use Amp\Success;
use Prooph\EventStore\Async\EventAppearedOnPersistentSubscription;
use Prooph\EventStore\Async\EventStorePersistentSubscription;
use Prooph\EventStore\ResolvedEvent;
use Psa\EventSourcing\Projection\StdoutTrait;

/**
 * Stdout Projection
 *
 * Writes a stream to STDOUT, useful for debugging
 */
class StdoutPersistentSubscription implements EventAppearedOnPersistentSubscription
{
	use StdoutTrait;

	/**
	 * @var null|\Prooph\EventStore\EventAppearedOnPersistentSubscription
	 */
	protected $subscription;

	/**
	 * @param null|\Prooph\EventStore\EventAppearedOnPersistentSubscription $subscription Subscription
	 */
	public function __construct(?EventAppearedOnPersistentSubscription $subscription = null)
	{
		$this->subscription = $subscription;
	}

	/**
	 * @inheritDoc
	 */
	public function __invoke(
		EventStorePersistentSubscription $subscription,
		ResolvedEvent $resolvedEvent,
		?int $retryCount = null
	): Promise {
		$this->writeEventToStdOut($resolvedEvent);

		if ($this->subscription !== null) {
			$callback = $this->subscription;

			return $callback($subscription, $resolvedEvent, $retryCount);
		}

		return new Success();
	}
}
