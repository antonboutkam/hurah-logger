<?php

namespace Test\Hurah\Logger;

use Hurah\Logger\LevelInterface;
use Hurah\Logger\LoggerInfoHelper;
use PHPUnit\Framework\TestCase;

class LoggerInfoHelperTest extends TestCase
{

	public function testFactory(): void
	{
		$aLevels = [];
		$oLogLevelIterator = LoggerInfoHelper::getLogLevels();
		foreach ($oLogLevelIterator as $oLogLevel)
		{
			$aLevels[$oLogLevel->getLevel()] = $oLogLevel->getLevelName();
		}

		$this->assertEquals('debug', $aLevels[100]);
		$this->assertEquals('info', $aLevels[200]);
		$this->assertEquals('notice', $aLevels[250]);
		$this->assertEquals('warning', $aLevels[300]);
		$this->assertEquals('error', $aLevels[400]);
		$this->assertEquals('critical', $aLevels[500]);
		$this->assertEquals('alert', $aLevels[550]);;
		$this->assertEquals('emergency', $aLevels[600]);;

	}
}
