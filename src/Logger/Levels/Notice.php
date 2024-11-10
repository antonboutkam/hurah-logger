<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Notice extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 250;
	}

	public function getLevelName(): string
	{
		return 'notice';
	}


}
