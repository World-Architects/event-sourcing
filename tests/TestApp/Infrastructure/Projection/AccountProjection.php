<?php
declare(strict_types = 1);

namespace App\Infrastructure\Projection\Accounting;

use App\Infrastructure\Persistence\PdoHelper;
use App\Infrastructure\Projection\AbstractProjection;
use Prooph\EventStore\RecordedEvent;
use Prooph\EventStore\ResolvedEvent;
use PDO;

/**
 * Account Projection
 */
class AccountProjection extends AbstractProjection
{
	/**
	 * @var string
	 */
	protected $tableName = 'accounts';

	/**
	 * @var string
	 */
	protected $primaryKey = 'accountId';

	/**
	 * @param \Prooph\EventStore\RecordedEvent; $event Event
	 * @param \Prooph\EventStore\ResolvedEvent; $resolvedEvent Resolved Event
	 * @return void
	 */
	public function created(RecordedEvent $event, ResolvedEvent $resolvedEvent): void
	{
		$this->pdoHelper->insert($this->getDataFromEvent($event));
	}

	/**
	 * @param \Prooph\EventStore\RecordedEvent; $event Event
	 * @param \Prooph\EventStore\ResolvedEvent; $resolvedEvent Resolved Event
	 * @return void
	 */
	public function updated(RecordedEvent $event, ResolvedEvent $resolvedEvent): void
	{
		$this->pdoHelper->update($this->getDataFromEvent($event), ['accountId' => 'id']);
	}
}
