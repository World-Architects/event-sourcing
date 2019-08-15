<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Projection;

/**
 * Abstract Read Model
 */
abstract class AbstractReadModel implements ReadModelInterface
{
	/**
	 * @var array
	 */
	private $stack = [];

	/**
	 * @inheritDoc
	 */
	public function stack(string $operation, ...$args): void
	{
		$this->stack[] = [$operation, $args];
	}

	/**
	 * @inheritDoc
	 */
	public function persist(): void
	{
		foreach ($this->stack as list($operation, $args)) {
			$this->{$operation}(...$args);
		}

		$this->stack = [];
	}
}
