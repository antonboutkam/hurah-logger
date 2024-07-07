<?php

namespace Hurah\Logger;

use Core\Environment;
use Exception;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Exception\LogicException;
use Hurah\Types\Exception\RuntimeException;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PhpNamespace;
use Hurah\Types\Util\ArrayUtils;
use Hurah\Types\Util\JsonUtils;
use Monolog\Handler\HandlerInterface;
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
	private static bool $bAddMethodName = true;
	private static bool $bAddFileName = true;
	private array $aGlobalContext = [];

	private LoggerInterface $oLoggerImplementation;

	public function __construct(int $iMinLogLevel = null, Path $oLogDir = null, string $sName = 'hurah')
	{
		if ($iMinLogLevel) {
			self::$iMinLogLevel = $iMinLogLevel;
		}

		if ($oLogDir) {
			self::$oLogDir = $oLogDir;
		}

		if (!isset(self::$oLogDir)) {
			self::$oLogDir = Path::make('/tmp');
		}

		$this->oLoggerImplementation = new MonoLogger($sName);
		// Log anything that is more important then the current minimal log level to combined.log.
		$mCombinedLog = self::getLogDir()->extend(self::COMBINED_LOG_FILE);
		$combinedHandler = new StreamHandler("{$mCombinedLog}", self::getMinLogLevel());
		$this->oLoggerImplementation->pushHandler($combinedHandler);

		// Log all warnings, critical, errors etc also separately.
		$mErrorLog = self::getLogDir()->extend(self::ERROR_LOG_FILE);
		$errorHandler = new StreamHandler("{$mErrorLog}", self::WARNING);
		$this->oLoggerImplementation->pushHandler($errorHandler);
	}

	public static function getLogDir(): Path
	{
		return self::$oLogDir;
	}

	private static function getMinLogLevel(): int
	{
		return self::$iMinLogLevel;
	}

	public static function setLogDir(Path $oLogDir): void
	{
		self::$oLogDir = $oLogDir;
	}

	public static function setMinLogLevel($iLogLevel = self::INFO): void
	{
		self::$iMinLogLevel = $iLogLevel;
	}

	public static function addMethodName(bool $bAddMethodName): void
	{
		self::$bAddMethodName = $bAddMethodName;
	}

	public static function addFileName(bool $bAddFileName): void
	{
		self::$bAddFileName = $bAddFileName;
	}

	/**
	 * Add additional logger handler
	 * For example new StreamHandler('php://stdout', Logger::WARNING)); to add logging to stdout
	 *
	 * @param HandlerInterface $oHandler
	 */
	public function addMonologHandler(HandlerInterface $oHandler): void
	{
		$this->oLoggerImplementation->pushHandler($oHandler);
	}

	public function addContext(...$context): self
	{
		$this->aGlobalContext = array_merge($this->aGlobalContext, $context);
		return $this;
	}

	public function setContext(...$context): self
	{
		$this->aGlobalContext = $context;
		return $this;
	}

	public function unsetContext(...$sKeys): self
	{
		if (empty($sKeys)) {
			return $this;
		}
		if (ArrayUtils::isAssociative($sKeys)) {
			$aKeys = array_keys($sKeys);
		}
		elseif (ArrayUtils::isSequential($sKeys)) {
			$aKeys = $sKeys;
		}
		else {
			throw new RuntimeException(__METHOD__ . " is expecting an associative array where the keys relate to the context keys that need to be unset or a sequential array with just the keys.");
		}
		foreach ($aKeys as $sKey) {
			if (isset($this->aGlobalContext[$sKey])) {
				unset($this->aGlobalContext[$sKey]);
			}
		}
		return $this;
	}

	public function clearContext(): self
	{
		$this->aGlobalContext = [];
		return $this;
	}

	/**
	 * Send log messages to any location + php://stdtout. Accepts an absolute or a relative path. If the path is
	 * relative it will be relative to self::$oLogDir
	 *
	 * @param $message
	 * @param $mLogFileName
	 * @param int $iLogLevel
	 * @param array $aContext
	 */
	public function custom($message, $mLogFileName, int $iLogLevel = self::DEBUG, array $aContext = []): void
	{
		$aContext = $this->processContext($aContext);
		$bIsRelative = self::isLogfilePathRelative($mLogFileName);
		if ($bIsRelative) {
			$mLogFileName = self::getLogDir()->extend($mLogFileName);
		}
		$log = new MonoLogger('custom');
		$log->pushHandler(new StreamHandler("{$mLogFileName}", $iLogLevel));
		$log->pushHandler(new StreamHandler('php://stdout', $iLogLevel));

		$log->log($iLogLevel, $message, $aContext);
	}

	private function processContext(array $aContext = []): array
	{
		return array_merge($this->aGlobalContext, $aContext);
	}

	public function log($level, $message, array $context = [])
	{
		$context = $this->processContext($context);
		if (self::$bAddFileName || self::$bAddMethodName) {
			$aTrace = debug_backtrace();
			foreach ($aTrace as $aTraceLine) {
				if (!isset($aTraceLine['class'])) {
					continue;
				}
				if ($aTraceLine['class'] != self::class) {
					break;
				}
			}
		}
		if (self::$bAddMethodName && isset($aTraceLine)) {
			try {
				$aComponents = [];
				if (isset($aTraceLine['class'])) {
					$oClass = PhpNamespace::make($aTraceLine['class']);
					$aComponents[] = $oClass->getShortName();
				}
				if (isset($aTraceLine['function'])) {
					$aTraceLine['function'] = Util::stripNamespace($aTraceLine['function']);
					$aComponents[] = $aTraceLine['function'];
				}
				if (!empty($aComponents)) {
					$context[] = join('::', $aComponents);
				}

			}
			catch (LogicException $e) {

			}

		}

		if (self::$bAddFileName && isset($aTraceLine)) {
			$context[] = basename($aTraceLine['file']) . ':' . $aTraceLine['line'];
		}

		$this->oLoggerImplementation->log($level, $message, $context);
	}

	/**
	 * Logs a record in the page-not-fond.log file and also logs it to PhPConsole when the current environment is
	 * set to DEVEL or TEST
	 *
	 * @param $sMessage
	 *
	 * @throws Exception
	 */
	public function pageNotFound($message, array $context = []): void
	{
		$context = $this->processContext($context);
		$log = new MonoLogger('404');
		$log->pushHandler(new StreamHandler(self::getLogDir() . '/page-not-found.log', self::getMinLogLevel()));
		if (class_exists('\\Core\\Environment') && Environment::isDevel() || Environment::isTest()) {
			$log->pushHandler(new PHPConsoleHandler(['enabled' => true]));
		}
		$log->error($message, $context);
	}

	public function error($message, array $context = []): void
	{
		$context = $this->processContext($context);
		$this->oLoggerImplementation->error($message, $context);
	}

	/**
	 * @param $mMessage
	 * @param string $sLevel
	 * @param array $context
	 *
	 * @throws InvalidArgumentException
	 */
	public function console($mMessage, string $sLevel = 'info', array $context = []): void
	{
		$context = $this->processContext($context);
		if (is_array($mMessage) || is_object($mMessage)) {
			$mMessage = JsonUtils::encode($mMessage);
		}
		$log = new MonoLogger('debug');
		$log->pushHandler(new PHPConsoleHandler(['enabled' => true]));

		if ($sLevel == 'info') {
			$log->info($mMessage);
		}
		else {
			if ($sLevel == 'warning') {
				$log->warning($mMessage, $context);

			}
			else {
				$log->debug($mMessage, $context);

			}
		}
	}

	public function info($message, array $context = []): void
	{
		$this->log(self::INFO, $message, $context);
	}

	public function warning($message, array $context = []): void
	{
		$this->log(self::WARNING, $message, $context);
	}

	public function debug($message, array $context = []): void
	{
		$this->log(self::DEBUG, $message, $context);
	}

	/**
	 * Log multiple messages at once
	 *
	 * @param array $aMessages [level=>int, message=>string], string[], anything Monolog supports
	 * @param int $iDefaultLevel
	 * @param array $context
	 *
	 * @return void
	 * @throws InvalidArgumentException
	 */
	public function multiple(array $aMessages, int $iDefaultLevel = self::DEBUG, array $context = []): void
	{
		$context = $this->processContext($context);
		foreach ($aMessages as $mMessage) {
			if (is_string($mMessage)) {
				$this->log($iDefaultLevel, $mMessage, $context);
			}
			elseif (is_array($mMessage) && isset($mMessage['message'])) {
				$this->log($mMessage['level'] ?? $iDefaultLevel, $mMessage['message'], $context);
			}
			elseif (is_array($mMessage)) {
				$this->log($mMessage['level'] ?? $iDefaultLevel, JsonUtils::encode($mMessage), $context);
			}
			else {
				$this->log($mMessage['level'] ?? $iDefaultLevel, $mMessage, $context);
			}
		}
	}

	public function emergency($message, array $context = []): void
	{
		$this->log(self::EMERGENCY, $message, $context);
	}

	public function alert($message, array $context = []): void
	{
		$this->log(self::ALERT, $message, $context);
	}

	public function critical($message, array $context = []): void
	{
		$this->log(self::CRITICAL, $message, $context);
	}

	public function notice($message, array $context = []): void
	{
		$this->log(self::NOTICE, $message, $context);
	}

	/**
	 * @param $mLogFileName
	 *
	 * @return bool
	 */
	public static function isLogfilePathRelative(string $mLogFileName): bool
	{

		if(str_starts_with($mLogFileName, 'php://'))
		{
			return false;
		}
		$mPathSeparatorPos = strpos($mLogFileName, DIRECTORY_SEPARATOR);
		$bIsRelative = false;
		if (is_int($mPathSeparatorPos) && $mPathSeparatorPos !== 0) {
			$bIsRelative = true;
		}
		elseif (is_bool($mPathSeparatorPos) && $mPathSeparatorPos === false) {
			$bIsRelative = true;
		}
		return $bIsRelative;
	}
}
