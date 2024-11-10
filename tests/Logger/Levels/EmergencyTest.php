<?php

namespace Test\Hurah\Logger\Levels;
use Hurah\Logger\Levels\Emergency;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class EmergencyTest extends TestCase
{


	public function testGetLevel(): void
	{
		$this->assertEquals(600, (new Emergency())->getLevel());;
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('emergency', (new Emergency())->getLevelName());;
	}


}
