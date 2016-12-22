<?php
namespace Finance\Core;

use Finance\Core\Exceptions\ApplicationException;

/**
 * Class LogWriter
 * @package Finance\Core
 */
class LogWriter
{
    /**
     * @var LogWriter
     */
    private static $instance;
    /**
     * @var string
     */
    private $pathToLogFolder;
    /**
     * @var string
     */
    private $errorFileName = "error.log";

    /**
     * @param $pathToLogFolder
     */
    public static function initLogWriter($pathToLogFolder)
    {
        static::$instance = new self();
        static::$instance->pathToLogFolder = $pathToLogFolder;
    }

    /**
     * @return LogWriter
     * @throws ApplicationException
     */
    public static function getInstance(): LogWriter
    {
        if (static::$instance === null) {
            throw new ApplicationException("Объект 'LogWriter' не инициализирован! ");
        }
        return static::$instance;
    }

    /**
     * @param string $message
     */
    public function write(string $message)
    {
        $fileManager = new FileManager();
        $fileManager->appendFile($this->errorFileName, $this->pathToLogFolder, $message);
    }
}