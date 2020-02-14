<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Domain\ReflectionBased\Event;

use Psa\EventSourcing\Test\TestApp\Domain\ReflectionBased\AccountId;

/**
 * Account Created Event
 */
class AccountUpdated
{
	public const EVENT_TYPE = 'Accounting.Account.updated';

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
		$self = new self();
		$self->accountId = (string)$accountId;
		$self->name = $name;
		$self->description = $description;

		return $self;
	}

	public function accountId(): AccountId
	{
		return AccountId::fromString($this->accountId);
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
