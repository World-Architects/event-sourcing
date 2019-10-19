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
	const EVENT_TYPE = 'Accounting.Account.updated';

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

	public function accountId(): AccountId
	{
		if ($this->accountId === null) {
			$this->accountId = AccountId::fromString($this->payload['accountId']);
		}

		return $this->accountId;
	}

	public function name(): string
	{
		if ($this->name === null) {
			$this->name = $this->payload['name'];
		}

		return $this->name;
	}

	public function description(): string
	{
		if ($this->description === null) {
			$this->description = $this->payload['description'];
		}

		return $this->description;
	}
}
