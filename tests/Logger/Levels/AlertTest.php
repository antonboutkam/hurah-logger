<?php

namespace Test\Hurah\Logger\Levels;

use Hurah\Logger\Levels\Alert;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class AlertTest extends TestCase
{


	public function testGetLevel(): void
	{

		$this->assertEquals(550, (new Alert())->getLevel());;
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('alert', (new Alert())->getLevelName());;
	}


}
