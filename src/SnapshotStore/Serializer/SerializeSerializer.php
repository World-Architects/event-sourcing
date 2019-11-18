<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace Psa\EventSourcing\SnapshotStore\Serializer;

/**
 * Serialize Serializer
 */
class SerializeSerializer implements SerializerInterface
{
	/**
	 * @inheritDoc
	 */
	public function serialize($data): string
	{
		return serialize($data);
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize(string $data)
	{
		return unserialize($data);
	}
}
