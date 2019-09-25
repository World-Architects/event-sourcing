<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestApp\Domain\Repository;

use Psa\EventSourcing\Aggregate\AbstractAggregateRepository;
use Psa\EventSourcing\Test\TestApp\Domain\Account;

/**
 * Account Repository
 */
class AccountRepository extends AbstractAggregateRepository
{
	/**
	 * Saves the account
	 *
	 * @param \Psa\EventSourcing\Test\TestApp\Domain\Account $account Account
	 * @return mixed
	 */
	public function save(Account $account)
	{
		return $this->saveAggregate($account);
	}
}
