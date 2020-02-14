<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased;

use JsonSerializable;
use Psa\EventSourcing\Aggregate\AggregateRoot;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Event\AccountCreated;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Event\AccountUpdated;

/**
 * Account Aggregate
 */
final class Account extends AggregateRoot implements JsonSerializable
{
	public const AGGREGATE_TYPE = 'Account';

	/**
	 * @var \App\Domain\Accounting\Model\AccountId
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var null|string
	 */
	protected $description;

	/**
	 * Create
	 *
	 * @param \App\Domain\Accounting\Model\AccountNumber $number Account Number
	 * @param string $name Name
	 * @param string $description Description
	 */
	public static function create(
		string $name,
		string $description
	) {
		$account = new self();
		$account->id = AccountId::generate();

		$account->recordThat(AccountCreated::create(
			$account->id,
			$name,
			$description,
		));

		return $account;
	}

	/**
	 * Updates name and description
	 *
	 * @param string $name Name
	 * @param string $description Description
	 * @return $this
	 */
	public function update(string $name, string $description)
	{
		$this->recordThat(AccountUpdated::create(
			AccountId::fromString((string)$this->id),
			$name,
			$description
		));

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function aggregateId(): string
	{
		return (string)$this->id;
	}

	/**
	 * @param \App\Domain\Accounting\Model\Event\AccountCreated $event Event
	 */
	public function whenAccountCreated(AccountCreated $event): void
	{
		$this->id = $event->accountId();
		$this->name = $event->name();
		$this->description = $event->description();
	}

	/**
	 * @param \App\Domain\Accounting\Model\Event\AccountUpdated $event Event
	 */
	public function whenAccountUpdated(AccountUpdated $event): void
	{
		$this->name = $event->name();
		$this->description = $event->description();
	}

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'id' => (string)$this->id,
			'name' => $this->name,
			'description' => $this->description,
		];
	}
}
