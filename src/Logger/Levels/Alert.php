<?php

namespace Hurah\Logger\Levels;

use Hurah\Logger\AbstractLevel;
use Hurah\Logger\LevelInterface;

/**
 *
 */
class Alert extends AbstractLevel implements LevelInterface
{


	public function getLevel(): int
	{
		return 550;
	}

	public function getLevelName(): string
	{
		return 'alert';
	}


}
