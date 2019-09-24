<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Domain\Event;

use Psa\EventSourcing\Aggregate\Event\AggregateChangedEvent;
use Psa\EventSourcing\Test\TestApp\Domain\AccountId;

/**
 * Account Created Event
 */
class AccountUpdated extends AggregateChangedEvent
{
	protected $accountId;
	protected $name;
	protected $description;

	/**
	 *
	 */
	public static function create(
		AccountId $accountId,
		string $name,
		string $description
	) {
		$event = self::occur((string)$accountId, [
			'accountId' => (string)$accountId,
			'name' => $name,
			'description' => $description,
		]);

		$event->accountId = $accountId;
		$event->name = $name;
		$event->description = $description;

		return $event;
	}

	/**
	 * @return string
	 */
	public function accountId(): string
	{
		return $this->accountId;
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function description(): string
	{
		return $this->description;
	}
}

