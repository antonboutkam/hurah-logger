<?php

namespace Test\Hurah\Logger\Levels;
use Hurah\Logger\Levels\Error;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class ErrorTest extends TestCase
{


	public function testGetLevel(): void
	{
		$this->assertEquals(400, (new Error())->getLevel());;
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('error', (new Error())->getLevelName());;
	}


}
