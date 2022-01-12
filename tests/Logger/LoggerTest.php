<?php

namespace Test\Hurah\Logger;

use DirectoryIterator;
use Hurah\Logger\Logger;
use Hurah\Types\Type\Path;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use function strpos;

class LoggerTest extends TestCase
{


    public function cleanupFiles(): void
    {
        $oErrorLogDir = $this->getLogDir();

        // Remove directory
        if ($oErrorLogDir->exists())
        {
            $oErrorLogDir->unlinkRecursive();
        }
    }

    public function setUp(): void
    {
        $this->cleanupFiles();
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

    public function testConstruct(): void
    {
        $this->assertInstanceOf(Logger::class, new Logger());
    }

    public function testInfo(): void
    {

        $oLogger = new Logger(Logger::DEBUG, $this->getLogDir(), 'hurah');
        $oLogger->info("Testing");

        $this->assertFileExists($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileDoesNotExist($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

    public function testWarning(): void
    {

        $oLogger = new Logger(Logger::DEBUG, $this->getLogDir(), 'hurah');
        $oLogger->warning("Testing");

        $this->assertFileExists($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileExists($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

    public function testNotLogging(): void
    {

        $oLogger = new Logger(Logger::WARNING, $this->getLogDir(), 'hurah');
        $oLogger->info("Testing");

        $this->assertFileDoesNotExist($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileDoesNotExist($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

    public function testLogging(): void
    {

        $oLogger = new Logger(Logger::WARNING, $this->getLogDir(), 'hurah');
        $oLogger->critical("Testing");

        $this->assertFileExists($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileExists($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

    private function getLogDir(): Path
    {
        return new Path('./tmp');
    }

}
