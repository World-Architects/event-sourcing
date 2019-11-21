<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\Projection\Async;

use Prooph\EventStore\Async\CatchUpSubscriptionDropped;
use Prooph\EventStore\Async\EventAppearedOnCatchupSubscription;
use Prooph\EventStore\Async\EventStoreCatchUpSubscription;
use Prooph\EventStore\ResolvedEvent;
use Prooph\EventStore\SubscriptionDropReason;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

/**
 * PsrLogConnectionDropped
 */

class PsrLogConnectionDropped implements CatchUpSubscriptionDropped
{
	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * @var \Prooph\EventStore\Async\CatchUpSubscriptionDropped
	 */
	protected $subscriptionDropped;

	/**
	 * @param \Psr\Log\LoggerInterface $logger PSR Logger
	 * @param mixed $logLevel PSR Log Level
	 * @param null|\Prooph\EventStore\Async\CatchUpSubscriptionDropped $subscriptionDropped Subscription Dropped
	 */
	public function __construct(
		LoggerInterface $logger,
		$logLevel = null,
		?CatchUpSubscriptionDropped $subscriptionDropped = null
	) {
		$this->logger = $logger;
		$this->logLevel = $logLevel === null ? LogLevel::ALERT : $logLevel;
		$this->subscriptionDropped = $subscriptionDropped;
	}

	/**
	 * @param \Prooph\EventStore\Async\EventStoreCatchUpSubscription $subscription Subscription
	 * @param \Prooph\EventStore\SubscriptionDropReason $reason Reason
	 * @param null|\Throwable $exception Throwable
	 */
	public function __invoke(
		EventStoreCatchUpSubscription $subscription,
		SubscriptionDropReason $reason,
		?Throwable $exception = null
	): void {
		$this->logger->log(
			$this->logLevel,
			'Event Store connection dropped!'
		);

		if ($this->subscriptionDropped) {
			$callable = $this->subscriptionDropped;
			$callable();
		}
	}
}
