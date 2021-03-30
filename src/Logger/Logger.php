<?php
namespace Hurah\Logger;

use Exception;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Util\JsonUtils;
use Hurah\Types\Type\Path;
use Monolog\Handler\PHPConsoleHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{

    const COMBINED_LOG_FILE = 'combined.log';
    const ERROR_LOG_FILE = 'error.log';

    /**
     * Detailed debug information
     */
    const DEBUG = 100;
    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;
    /**
     * Uncommon events
     */
    const NOTICE = 250;
    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;
    /**
     * Runtime errors
     */
    const ERROR = 400;
    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;
    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;
    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;

    private static int $iMinLogLevel = self::WARNING;
    private static Path $oLogDir;

    private LoggerInterface $oLoggerImplementation;

    public function __construct(int $iMinLogLevel = null, Path $oLogDir = null, string $sName = 'hurah')
    {
        if($iMinLogLevel)
        {
            self::$iMinLogLevel = $iMinLogLevel;
        }

        if ($oLogDir)
        {
            self::$oLogDir = $oLogDir;
        }
        else if(!self::$oLogDir)
        {
            self::$oLogDir =  Path::make('./tmp');
        }

        $this->oLoggerImplementation = new MonoLogger($sName);
        // Log anything that is more important then the current minimal log level to combined.log.
        $mCombinedLog = self::getLogDir()->extend(self::COMBINED_LOG_FILE);
        $combinedHandler = new StreamHandler("{$mCombinedLog}", self::getMinLogLevel());
        $this->oLoggerImplementation->pushHandler($combinedHandler);

        // Log all warnings, critical, errors etc also separately.
        $mErrorLog = self::getLogDir()->extend(self::ERROR_LOG_FILE);
        $errorHandler = new StreamHandler(self::getLogDir()->extend("{$mErrorLog}"), self::WARNING);
        $this->oLoggerImplementation->pushHandler($errorHandler);
    }

    public static function getLogDir(): Path
    {
        return self::$oLogDir;
    }
    public static function setLogDir(Path $oLogDir): void
    {
        self::$oLogDir = $oLogDir;
    }

    private static function getMinLogLevel()
    {
        return self::$iMinLogLevel;
    }
    public static function setMinLogLevel($iLogLevel = self::INFO)
    {
        self::$iMinLogLevel = $iLogLevel;
    }

    /**
     * Send log messages to any location + php://stdtout. Accepts an absolute or a relative path. If the path is
     * relative it will be relative to self::$oLogDir
     *
     * @param $sMessage
     * @param $mLogFileName
     * @param int $iLogLevel
     * @throws Exception
     */
    public function custom($sMessage, $mLogFileName, $iLogLevel = self::DEBUG)
    {
        if (!strpos($mLogFileName, PATH_SEPARATOR) === 0) {
            $mLogFileName = self::getLogDir()->extend($mLogFileName);
        }
        $log = new MonoLogger('custom');
        $log->pushHandler(new StreamHandler("{$mLogFileName}", $iLogLevel));
        $log->pushHandler(new StreamHandler('php://stdout', $iLogLevel));

        $log->addInfo($sMessage);
    }

    /**
     * Logs a record in the page-not-fond.log file and also logs it to PhPConsole when the current environment is
     * set to DEVEL or TEST
     * @param $sMessage
     * @throws Exception
     */
    public function pageNotFound($sMessage)
    {
        $log = new MonoLogger('404');
        $log->pushHandler(new StreamHandler(self::getLogDir() . '/page-not-found.log', self::getMinLogLevel()));
        if (class_exists('\\Core\\Environment') && \Core\Environment::isDevel() || \Core\Environment::isTest()) {
            $log->pushHandler(new PHPConsoleHandler(['enabled' => true]));
        }

        $log->addError($sMessage);
        self::error($sMessage);
    }

    public function error($sMessage, array $context = array())
    {
        $this->oLoggerImplementation->error($sMessage, $context);
    }

    /**
     * @param $mMessage
     * @param string $sLevel
     * @throws InvalidArgumentException
     */
    public function console($mMessage, string $sLevel = 'info')
    {
        if (is_array($mMessage) || is_object($mMessage)) {
            $mMessage = JsonUtils::encode($mMessage);
        }
        $log = new MonoLogger('debug');
        $log->pushHandler(new PHPConsoleHandler(['enabled' => true]));

        if ($sLevel == 'info') {
            $log->addInfo($mMessage);
        } else if ($sLevel == 'warning') {
            $log->addWarning($mMessage);

        } else {
            $log->addDebug($mMessage);

        }
    }

    public function warning($sMessage, array $context = array())
    {
        $this->oLoggerImplementation->warning($sMessage, $context);
    }

    public function info($sMessage, array $context = array())
    {
        echo "Write info " . self::getMinLogLevel();
        $this->oLoggerImplementation->info($sMessage, $context);
    }

    public function debug($sMessage, array $context = array())
    {
        $this->oLoggerImplementation->debug($sMessage, $context);
    }

    public function emergency($message, array $context = array())
    {
        $this->oLoggerImplementation->emergency($message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->oLoggerImplementation->alert($message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->oLoggerImplementation->critical($message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->oLoggerImplementation->notice($message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->oLoggerImplementation->log($level, $message, $context);
    }
}
