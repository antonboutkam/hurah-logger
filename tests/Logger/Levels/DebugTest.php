<?php

namespace Test\Hurah\Logger\Levels;
use Hurah\Logger\Levels\Debug;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class DebugTest extends TestCase
{


	public function testGetLevel(): void
	{
		$this->assertEquals(100, (new Debug())->getLevel());;
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('debug', (new Debug())->getLevelName());;
	}


}
