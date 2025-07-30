<?php

namespace Test\Hurah\Logger\Levels;

use Hurah\Logger\Levels\Notice;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class NoticeTest extends TestCase
{


	public function testGetLevel(): void
	{
		$this->assertEquals(250, (new Notice())->getLevel());;
	}

	public function testGetLevelName(): void
	{
		$this->assertEquals('notice', (new Notice())->getLevelName());;
	}


}
