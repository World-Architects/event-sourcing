<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Projection;

use Prooph\EventStore\EventAppearedOnPersistentSubscription;
use Prooph\EventStore\EventStoreCatchUpSubscription;
use Prooph\EventStore\EventStorePersistentSubscription;
use Prooph\EventStore\ResolvedEvent;

use function Amp\call;

/**
 * Stdout Projection
 *
 * Writes a stream to STDOUT, useful for debugging
 */
class StdoutPersistentProjection implements EventAppearedOnPersistentSubscription
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
	): void {
		$this->writeEventToStdOut($resolvedEvent);

		if ($this->subscription !== null) {
			$callback = $this->subscription;
			$callback($subscription, $resolvedEvent, $retryCount);
		}
	}
}
