<?php
namespace Finance;

use Finance\Controllers\ErrorController;
use Finance\Core\Config;
use Finance\Core\Database;
use Finance\Core\Exceptions\ApplicationException;
use Finance\Core\Exceptions\RouterNotFoundRouteException;
use Finance\Core\LogWriter;
use Finance\Core\Router;

/**
 * Class Router
 *
 * @package Finance
 */
class Application
{
    const CONTROLLER = 'controller';
    const ACTION = 'action';

    /**
     * @var string
     */
    private static $pathToConfigFile = __DIR__ . "/Configs/config.json";
    /**
     * @var string
     */
    private static $pathToConfigParametersFile = __DIR__ . "/Configs/configParameters.json";


    public static function startSession()
    {
        session_start();
    }

    /**
     * @param array $server
     * @param array $request
     * @param array $session
     */
    public static function launch(array $server, array $request, array $session)
    {
        try {
            self::importConfig(self::$pathToConfigFile, self::$pathToConfigParametersFile);
            self::initLogWriter();
            self::initDBConn('localhost', 'root', 'password', 'Shop');
            self::activateRouter($server, $request);
            $currentUrl = Router::getInstance()->getUrl();
            $route = Router::getInstance()->getRoute($currentUrl);
            $controller = Router::getInstance()->getController($route['controller']);
            $controller->action($server, $request, $session);
        } catch (RouterNotFoundRouteException $exception) {
            (new ErrorController())->action($server, ['errorMessage' => $exception->getMessage()], $session);
            self::logError("RouterNotFoundRoute exception!!!", $exception);
        } catch (ApplicationException $exception) {
            (new ErrorController())->action($server, ['errorMessage' => $exception->getMessage()], $session);
            self::logError("Finance exception!!!", $exception);
        } catch (\Exception $exception) {
            (new ErrorController())->action($server, ['errorMessage' => "Internal exception!",'errorContent' => $exception->getMessage()], $session);
            self::logError("Internal exception!!!", $exception);
        } catch (\Error $error) {
            (new ErrorController())->action($server, ['errorMessage' => "Internal error!",'errorContent' => $error->getMessage()], $session);
            self::logError("Internal error!!!", $error);
        }
    }

    /**
     * @param string $pathToConfigFile
     * @param string $pathToConfigParametersFile
     */
    private static function importConfig(string $pathToConfigFile, string $pathToConfigParametersFile)
    {
        Config::initConfig($pathToConfigFile, $pathToConfigParametersFile);
        ini_set("display_errors", Config::getInstance()->getShowErrorsOption());
    }

    /**
     * @param array $server
     * @param array $request
     */
    private static function activateRouter(array $server, array $request)
    {
        Router::initRouter($server, $request);
    }

    private static function initLogWriter()
    {
        LogWriter::initLogWriter(Config::getInstance()->getLogFolder());
        LogWriter::getInstance()->write("*****************************************************" . PHP_EOL);
        LogWriter::getInstance()->write("LogWriter инициализирован " . date("Y-m-d H:i:s") . PHP_EOL);
        LogWriter::getInstance()->write("*****************************************************" . PHP_EOL);
    }

    /**
     * @param $errorType
     * @param \Exception| \Error $error
     */
    private static function logError(string $errorType, $error)
    {
        LogWriter::getInstance()->write($errorType . PHP_EOL);
        LogWriter::getInstance()->write("Message: " . $error->getMessage() . PHP_EOL);
        LogWriter::getInstance()->write("Trace: " . $error->getTraceAsString() . PHP_EOL . PHP_EOL);
    }

    private static function initDBConn(string $host, string $user, string $password, string $dataBaseName)
    {
        Database::init($host, $user, $password, $dataBaseName);
    }
}