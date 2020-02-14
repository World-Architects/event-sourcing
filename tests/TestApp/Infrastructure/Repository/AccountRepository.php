<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Infrastructure\Repository;

use Psa\EventSourcing\Aggregate\AggregateRepository;
use Psa\EventSourcing\Aggregate\AggregateRoot;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Account;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\AccountId;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Event\AccountCreated;
use Psa\EventSourcing\Test\TestApp\Domain\InterfaceBased\Event\AccountUpdated;

/**
 * Account Repository
 */
class AccountRepository extends AggregateRepository
{
	public const AGGREGATGE_TYPE = [
		Account::AGGREGATE_TYPE => Account::class
	];

	protected $aggregateType = [
		Account::AGGREGATE_TYPE => Account::class
	];

	/**
	 * @inheritDoc
	 */
	protected $eventTypeMapping = [
		'Accounting.Account.created' => AccountCreated::class,
		'Accounting.Account.updated' => AccountUpdated::class
	];

	/**
	 * @param \Psa\EventSourcing\Test\TestApp\Domain\Account
	 */
	public function save(Account $account)
	{
		$this->saveAggregate($account);
	}

	/**
	 * @param \Psa\EventSourcing\Test\TestApp\Domain\AccountId
	 * @return Account
	 */
	public function get(AccountId $accountId): Account
	{
		return $this->getAggregate((string)$accountId);
	}
}
