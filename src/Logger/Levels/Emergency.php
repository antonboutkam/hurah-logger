<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Emergency extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 600;
	}

	public function getLevelName(): string
	{
		return 'emergency';
	}


}
