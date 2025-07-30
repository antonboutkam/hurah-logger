<?php

namespace Test\Hurah\Logger\Levels;

use Hurah\Logger\Levels\Info;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class InfoTest extends TestCase
{


	public function testGetLevel(): void
	{
		$this->assertEquals(200, (new Info())->getLevel());;
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('info', (new Info())->getLevelName());;
	}


}
