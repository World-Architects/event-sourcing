<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Projection;

/**
 * Read Model
 */
interface ReadModelInterface
{
	/**
	 * Initializes the model
	 *
	 * @return void
	 */
	public function init(): void;

	/**
	 * Checks if the model was initialized
	 *
	 * @return bool
	 */
	public function isInitialized(): bool;

	/**
	 * Resets the model
	 *
	 * @return void
	 */
	public function reset(): void;

	/**
	 * Delete
	 *
	 * @return void
	 */
	public function delete(): void;

	/**
	 * @return void
	 */
	public function stack(string $operation, ...$args): void;

	/**
	 * @return void
	 */
	public function persist(): void;
}
