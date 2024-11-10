<?php

namespace Hurah\Logger;

use Hurah\Types\Type\IGenericDataType;

interface LevelInterface extends IGenericDataType
{

	public function getLevel():int;
	public function getLevelName():string;

}
