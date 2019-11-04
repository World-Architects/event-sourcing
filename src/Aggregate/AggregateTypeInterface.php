<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Aggregate;

/**
 * Aggregate Type Interface
 */
interface AggregateTypeInterface
{
	/**
	 * @return null|string
	 */
	public function mappedClass(): ?string;

	/**
	 * @return string
	 */
	public function toString(): string;

	/**
	 * @return string
	 */
	public function __toString(): string;
}
