<?php
declare(strict_types=1);

namespace Psa\EventSourcing\Test\TestCase\EventStoreIntegration;

use PHPUnit\Framework\TestCase;
use Psa\EventSourcing\EventStoreIntegration\AggregateChangedEventTranslator;

/**
 * AggregateChangedEventTranslatorTest
 */
class AggregateChangedEventTranslatorTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testTranslator(): void
	{
		$translator = new AggregateChangedEventTranslator();
	}
}
