<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Domain;

use Psa\EventSourcing\Aggregate\AbstractAggregateRepository;
use Psa\EventSourcing\Test\TestApp\Domain\Event\AccountCreated;
use Psa\EventSourcing\Test\TestApp\Domain\Event\AccountUpdated;
use Psa\EventSourcing\Aggregate\AggregateRoot;

/**
 * Account
 */
class AccountRepository extends AbstractAggregateRepository
{
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
