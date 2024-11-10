<?php

namespace Test\Hurah\Logger\Levels;

use Hurah\Logger\Levels\Critical;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class CriticalTest extends TestCase
{


	public function testGetLevel(): void
	{
		$this->assertEquals(500, (new Critical())->getLevel());
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('critical', (new Critical())->getLevelName());;
	}

}
