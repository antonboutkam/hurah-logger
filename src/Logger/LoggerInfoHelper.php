<?php

namespace Hurah\Logger;

use Hurah\Logger\Levels\Alert;
use Hurah\Logger\Levels\Debug;
use Hurah\Logger\Levels\Emergency;
use Hurah\Logger\Levels\Error;
use Hurah\Logger\Levels\Info;
use Hurah\Logger\Levels\Notice;
use Hurah\Logger\Levels\Critical;
use Hurah\Logger\Levels\Warning;

/**
 * Here to provide additional information about logging and log levels
 */
class LoggerInfoHelper
{
	/**
	 * Returns a collection of Log levels and their numeric values
	 */
	public static function getLogLevels():LogLevelCollection
	{
		$oLogLevelCollection = new LogLevelCollection();
		$oLogLevelCollection->add(new Debug());
		$oLogLevelCollection->add(new Info());
		$oLogLevelCollection->add(new Notice());
		$oLogLevelCollection->add(new Warning());
		$oLogLevelCollection->add(new Error());
		$oLogLevelCollection->add(new Alert());
		$oLogLevelCollection->add(new Critical());
		$oLogLevelCollection->add(new Emergency());

		return $oLogLevelCollection;

	}
}
