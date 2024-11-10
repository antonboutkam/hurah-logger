<?php

namespace Hurah\Logger;

use Hurah\Types\Type\AbstractCollectionDataType;
use Hurah\Types\Type\IGenericDataType;

/**
 *
 */
class LogLevelCollection extends AbstractCollectionDataType
{


	public function current(): LevelInterface
	{
		return $this->array[$this->position];
	}

	public function add(LevelInterface $oItem): void
	{
		$this->array[] = $oItem;
	}
}
