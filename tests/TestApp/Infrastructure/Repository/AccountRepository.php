<?php

declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Infrastructure\Repository;

use Psa\EventSourcing\Aggregate\AbstractAggregateRepository;
use Psa\EventSourcing\Aggregate\AggregateRoot;
use Psa\EventSourcing\Test\TestApp\Domain\Account;
use Psa\EventSourcing\Test\TestApp\Domain\AccountId;
use Psa\EventSourcing\Test\TestApp\Domain\Event\AccountCreated;
use Psa\EventSourcing\Test\TestApp\Domain\Event\AccountUpdated;

/**
 * Account Repository
 */
class AccountRepository extends AbstractAggregateRepository
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
