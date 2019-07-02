<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\EventSourcing\Projection;

use Prooph\EventStore\EventAppearedOnCatchupSubscription;
use Prooph\EventStore\EventStoreCatchUpSubscription;
use Prooph\EventStore\ResolvedEvent;

/**
 * Stdout Projection
 *
 * Writes a stream to STDOUT, useful for debugging
 */
class StdoutProjection implements EventAppearedOnCatchupSubscription
{
	/**
	 * @var string
	 */
	protected $dateFormat = 'Y-m-d H:i:s.u';

	/**
	 * @var string
	 */
	protected $outputFormat = '{date} [{eventNumber}] {eventType}' . PHP_EOL;

	/**
	 * @inheritDoc
	 */
	public function __invoke(
		EventStoreCatchUpSubscription $subscription,
		ResolvedEvent $resolvedEvent
	): void {
		$event = $resolvedEvent->event();

		$vars = [
			'date' => $event->created()->format($this->dateFormat),
			'eventNumber' => $event->eventNumber(),
			'eventType' => $event->eventType(),
			'eventId' => $event->eventType(),
			'streamId' => $event->eventStreamId(),
		];

		$search = [];
		$replace = [];

		foreach ($vars as $key => $value) {
			$search[] = '{' . $key . '}';
			$replace[] = $value;
		}

		fwrite(STDOUT, str_replace($search, $replace, $this->outputFormat));
	}
}
