<?php
namespace Finance\Core;

use Finance\Core\Exceptions\ApplicationException;
use Finance\Core\Exceptions\RouterNotFoundRouteException;

/**
 * Class Router
 *
 * @package Finance\Core
 */
class Router
{
    const REQUEST_URI = 'REQUEST_URI';

    /**
     * @var Router
     */
    private static $instance;
    /**
     * @var array
     */
    private $server;
    /**
     * @var array
     */
    private $request;

    /**
     * @param array $server
     * @param array $request
     */
    public static function initRouter(array $server, array $request)
    {
        static::$instance = new self();
        static::$instance->server = $server;
        static::$instance->request = $request;
    }

    /**
     * @return Router
     * @throws ApplicationException
     */
    public static function getInstance(): Router
    {
        if (static::$instance === null) {
            throw new ApplicationException("Объект 'Router' не инициализирован! ");
        }

        return static::$instance;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->server[self::REQUEST_URI];
    }

    /**
     * @param string $url
     * @return array
     * @throws \Exception
     */
    public function getRoute(string $url): array
    {
        $allRoutes = $this->loadAllRoutes(Config::getInstance()->getPathToRouteFile());
        foreach ($allRoutes as $route) {
            $resultRoute = $this->findRouteForPatterns($route, $url);
            if ($resultRoute) {
                return $resultRoute;
            }
        }
        throw new RouterNotFoundRouteException("Unknown route for current URL! " . $url);
    }

    /**
     * @param array $route
     * @param string $url
     * @return array|null
     */
    private function findRouteForPatterns(array $route, string $url)
    {
        foreach ($route['patterns'] as $pattern) {
            $preparedPattern = $this->preparePattern($pattern);
            if (preg_match($preparedPattern, $url)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @param string $pattern
     * @return string
     */
    private function preparePattern(string $pattern): string
    {
        $resultPattern = str_replace('/', '\/', $pattern);
        if (preg_match('/\*$/', $resultPattern)) {
            $resultPattern = str_replace('*', '[a-z0-9\-_:]*$', $resultPattern);
            $resultPattern = '/^' . $resultPattern . '/';
        } else {
            $resultPattern = '/^' . $resultPattern . '$/';
        }

        return $resultPattern;
    }

    /**
     * @param string $pathToRoutesFile
     * @return array
     */
    private function loadAllRoutes(string $pathToRoutesFile): array
    {
        $this->assertExistsFile($pathToRoutesFile);

        return json_decode(file_get_contents($pathToRoutesFile), true);
    }

    /**
     * @param string $pathToRoutesFile
     * @throws \Exception
     */
    private function assertExistsFile(string $pathToRoutesFile)
    {
        if (!file_exists($pathToRoutesFile)) {
            throw new \Exception("Путь до файла routes.json указан неверно! Путь: " . $pathToRoutesFile);
        }
    }

    /**
     * @param string $page
     */
    public function redirectTo(string $page)
    {
        $host = 'http://' . $this->server['HTTP_HOST'] . '/';
        header('Location:' . $host . $page);
    }

    /**
     * @param string $controllerName
     * @return Controller
     */
    public function getController(string $controllerName): Controller
    {
        $controllerClassName = "Finance\\Controllers\\" . $controllerName;

        return new $controllerClassName();
    }
}