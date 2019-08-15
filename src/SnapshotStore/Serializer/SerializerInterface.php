<?php
declare(strict_types = 1);

namespace Psa\EventSourcing\SnapshotStore\Serializer;

/**
 * Serializer Interface
 */
interface SerializerInterface
{
	/**
	 * Serialize
	 *
	 * @param mixed $data Data to serialize
	 * @return string
	 */
	public function serialize($data): string;

	/**
	 * Unserialize
	 *
	 * @param string $data Serialized data
	 * @return mixed
	 */
	public function unserialize(string $data);
}
