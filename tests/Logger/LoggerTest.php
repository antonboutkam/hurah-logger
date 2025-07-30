<?php

namespace Test\Hurah\Logger;

use Hurah\Logger\Logger;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use function strpos;

class LoggerTest extends TestCase
{
	public function setUp(): void
	{
		$this->cleanupFiles();
	}

	public function cleanupFiles(): void
	{
		$oErrorLogDir = $this->getLogDir();

		// Remove directory
		if($oErrorLogDir->exists()) {
			$oErrorLogDir->unlinkRecursive();
		}
	}

	private function getLogDir(): Path
	{
		return new Path('./tmp');
	}

	public function tearDown(): void
	{
		$this->cleanupFiles();
	}

	public function testAddHandler()
	{
		$oExtraHandlerLogFile = $this->getLogDir()->extend('extra-handler.log');
		$oExtraHandlerLogFile->unlink();
		$sFile = "{$oExtraHandlerLogFile}";
		$oLogger = new Logger();
		$oLogger->addMonologHandler(new StreamHandler($sFile, Logger::WARNING));
		$oLogger->warning($sMsg = "blabla test");

		$this->assertTrue(strpos($oExtraHandlerLogFile->contents(), $sMsg) > 1);

		$this->assertTrue($oExtraHandlerLogFile->exists());
		$oExtraHandlerLogFile->unlink();
	}

	public function testStdout(): void
	{
		$this->assertFalse(Logger::isLogfilePathRelative('php://stdout'));
		$this->assertFalse(Logger::isLogfilePathRelative('/home/nuidev'));
		$this->assertTrue(Logger::isLogfilePathRelative('./home/nuidev'));
	}

	public function testConstruct(): void
	{
		$this->assertInstanceOf(Logger::class, new Logger());
	}

	public function getErrorFileName(): string
	{
		return (string)$this->getLogDir()->extend('error-' . date('Y-m-d') . '.log');
	}

	public function testInfo(): void
	{

		$oLogger = new Logger(Logger::DEBUG, $this->getLogDir(), 'hurah');
		$oLogger->info("Testing");

		$this->assertFileExists($this->getCombinedFileName());
		$this->assertFileDoesNotExist($this->getErrorFileName());
	}
	public function getCombinedFile(): Path
	{
		return $this->getLogDir()->extend('combined-' . date('Y-m-d') . '.log');
	}
	public function getCombinedFileName(): string
	{
		return (string)$this->getLogDir()->extend('combined-' . date('Y-m-d') . '.log');
	}

	public function testWarning(): void
	{

		$oLogger = new Logger(Logger::DEBUG, $this->getLogDir(), 'hurah');
		$oLogger->warning("Testing");

		$this->assertFileExists($this->getCombinedFileName());
		$this->assertFileExists($this->getErrorFileName());
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testMultiple(): void
	{

		$oLogger = new Logger(Logger::DEBUG, $this->getLogDir(), 'hurah');
		Logger::addFileName(false);
		Logger::addMethodName(false);
		$aLogItems = [
		  ['message' => 'Logging something', 'level' => Logger::WARNING],
		  ['message' => 'Logging another thing'],
		  ['And another'],
		  [['And', 'An', 'Array']],
		];
		$oLogger->multiple($aLogItems, Logger::INFO, ['log', 'multiple']);


		$this->assertFileExists($this->getCombinedFileName());
		$this->assertFileExists($this->getErrorFileName());

		$sLogFileContents = $this->getCombinedFile()->contents();
		$this->assertTrue(strpos($sLogFileContents, 'WARNING > Logging something ["log","multiple"]') > 0, $sLogFileContents);
		$this->assertTrue(strpos($sLogFileContents, 'INFO > Logging another thing ["log","multiple"]') > 0, $sLogFileContents);
		$this->assertTrue(strpos($sLogFileContents, 'INFO > ["And another"] ["log","multiple"] ') > 0, $sLogFileContents);
	}

	public function testNotLogging(): void
	{

		$oLogger = new Logger(Logger::WARNING, $this->getLogDir(), 'hurah');
		$oLogger->info("Testing");

		$this->assertFileDoesNotExist($this->getCombinedFileName());
		$this->assertFileDoesNotExist($this->getErrorFileName());
	}

	public function testCustom(): void
	{
		$sCustomFile = 'custom-file';
		$sCustomMessage = "This is a custom message";
		$aCustomTags = ['some', 'context'];
		$oCustomFilePath = $this->getLogDir()->extend($sCustomFile);

		$oLogger = new Logger(Logger::WARNING, $this->getLogDir(), 'hurah');
		$oLogger->custom($sCustomMessage, $sCustomFile, Logger::WARNING, $aCustomTags);

		$this->assertFileExists($oCustomFilePath);
		$this->assertTrue(strpos($oCustomFilePath->contents(), $sCustomMessage) > 0);
		$this->assertTrue(strpos($oCustomFilePath->contents(), 'some') > 0);
		$oCustomFilePath->unlink();
	}

	public function testLogging(): void
	{

		$oLogger = new Logger(Logger::WARNING, $this->getLogDir(), 'hurah');
		$oLogger->critical("Testing");

		$this->assertFileExists($this->getCombinedFileName());
		$this->assertFileExists($this->getErrorFileName());
	}
}
