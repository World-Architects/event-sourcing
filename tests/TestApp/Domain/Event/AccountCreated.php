<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Domain\Event;

use Psa\EventSourcing\Test\TestApp\Domain\AccountId;
use Psa\EventSourcing\Test\TestApp\Domain\Event\AggregateChangedEvent;

/**
 * Account Created Event
 */
class AccountCreated extends AggregateChangedEvent
{
	const EVENT_TYPE = 'Accounting.Account.created';

	protected $accountId;
	protected $accountNumber;
	protected $currency;
	protected $name;
	protected $description;

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
			$this->accountId = AccountId::fromString($this->payload['accountNumber']);
		}

		return $this->accountId;
	}

	public function name(): string
	{
		return $this->name;
	}

	public function description(): string
	{
		return $this->description;
	}
}