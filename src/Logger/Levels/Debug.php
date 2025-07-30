<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Debug extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 100;
	}

	public function getLevelName(): string
	{
		return 'debug';
	}


}
