<?php

namespace Test\Hurah\Logger;

use Hurah\Logger\Logger;
use Hurah\Types\Type\Path;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {


    private function getLogDir():Path
    {
        return new Path('./tmp');
    }
    public function cleanupFiles():void
    {
        $oErrorLogFile = $this->getLogDir()->extend(Logger::COMBINED_LOG_FILE);

        if($oErrorLogFile->exists())
        {
            $oErrorLogFile->unlink();
        }
        $oErrorLogFile = $this->getLogDir()->extend(Logger::ERROR_LOG_FILE);

        if($oErrorLogFile->exists())
        {
            $oErrorLogFile->unlink();
        }

        if($this->getLogDir()->isDir())
        {
            $this->getLogDir()->unlink();
        }

    }
    public function setUp():void
    {
        $this->cleanupFiles();
    }

    public function tearDown():void
    {
        $this->cleanupFiles();
    }

    public function testConstruct(): void {
        $this->assertInstanceOf(Logger::class, new Logger());
    }

    public function testInfo(): void {

        $oLogger = new Logger(Logger::DEBUG, $this->getLogDir(), 'hurah');
        $oLogger->info("Testing");

        $this->assertFileExists($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileDoesNotExist($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

    public function testWarning(): void {

        $oLogger = new Logger(Logger::DEBUG, $this->getLogDir(), 'hurah');
        $oLogger->warning("Testing");

        $this->assertFileExists($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileExists($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

    public function testNotLogging(): void {

        $oLogger = new Logger(Logger::WARNING, $this->getLogDir(), 'hurah');
        $oLogger->info("Testing");

        $this->assertFileDoesNotExist($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileDoesNotExist($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

    public function testLogging(): void {

        $oLogger = new Logger(Logger::WARNING, $this->getLogDir(), 'hurah');
        $oLogger->critical("Testing");

        $this->assertFileExists($this->getLogDir()->extend(Logger::COMBINED_LOG_FILE));
        $this->assertFileExists($this->getLogDir()->extend(Logger::ERROR_LOG_FILE));
    }

}
