<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Critical extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 500;
	}

	public function getLevelName(): string
	{
		return 'critical';
	}


}
