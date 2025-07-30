<?php

namespace Hurah\Logger;

use Hurah\Logger\LevelInterface;
use Hurah\Types\Type\IGenericDataType;

/**
 *
 */
abstract class AbstractLevel implements LevelInterface
{



	public function __construct($sValue = null) {}

	public function __toString(): string
	{
		return $this->getLevelName();
	}

	public function setValue($sValue)
	{

	}

	public function getValue(): int
	{
		return $this->getLevel();
	}

	abstract public function getLevel(): int;

	abstract public function getLevelName(): string;

}
