<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Warning extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 300;
	}

	public function getLevelName(): string
	{
		return 'warning';
	}


}
