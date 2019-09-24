<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore\Serializer;

/**
 * Json Serializer
 */
class JsonSerializer implements SerializerInterface
{
	/**
	 * @inheritDoc
	 */
	public function serialize($data): string
	{
		return json_encode($data);
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize(string $data)
	{
		return json_decode($data, true);
	}
}
