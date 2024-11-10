<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Error extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 400;
	}

	public function getLevelName(): string
	{
		return 'error';
	}


}
