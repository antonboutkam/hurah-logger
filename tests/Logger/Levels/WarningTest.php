<?php

namespace Test\Hurah\Logger\Levels;

use Hurah\Logger\Levels\Warning;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class WarningTest extends TestCase
{


	public function testGetLevel(): void
	{
		$this->assertEquals(300, (new Warning())->getLevel());;
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('warning', (new Warning())->getLevelName());;
	}


}
