<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Info extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 200;
	}

	public function getLevelName(): string
	{
		return 'info';
	}


}
