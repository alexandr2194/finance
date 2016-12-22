<?php
namespace Finance\Core;

use Finance\Core\Exceptions\ApplicationException;

/**
 * Class Configs
 * @package Finance\Configs
 */
class Config
{
    const APPLICATION = 'application';
    const PHP_SETTINGS = 'php-settings';
    const TEMPLATE = 'template';

    /**
     * @var Config
     */
    private static $instance;
    /**
     * @var array
     */
    private $configData;
    /**
     * @var array
     */
    private $configParameters;

    /**
     * @param string $pathToConfigFile
     * @param string $pathToConfigParametersFile
     */
    public static function initConfig(string $pathToConfigFile, string $pathToConfigParametersFile)
    {
        static::$instance = new self();

        static::$instance->assertExistsFile($pathToConfigFile);
        static::$instance->configData = json_decode(file_get_contents($pathToConfigFile), true);
        static::$instance->assertCorrectJsonDecode();

        static::$instance->assertExistsFile($pathToConfigFile);
        static::$instance->configParameters = json_decode(file_get_contents($pathToConfigParametersFile), true);
        static::$instance->assertCorrectJsonDecode();
    }

    /**
     * @return Config
     * @throws ApplicationException
     */
    public static function getInstance(): Config
    {
        if (static::$instance === null) {
            throw new ApplicationException("Объект 'Config' не инициализирован! ");
        }
        return static::$instance;
    }

    /**
     * @return bool
     */
    public function getShowErrorsOption(): bool
    {
        return $this->configData[self::PHP_SETTINGS]['display_errors'];
    }

    /**
     * @return string
     */
    public function getDebugsFolder(): string
    {
        return $this->preparePath($this->configData[self::APPLICATION]['debugsFolder']);
    }

    /**
     * @return string
     */
    public function getPathToRouteFile():string
    {
        return $this->preparePath($this->configData[self::APPLICATION]['routeFile']);
    }

    /**
     * @return string
     */
    public function getRootFolder(): string
    {
        return $this->preparePath($this->configData[self::APPLICATION]['root']);
    }

    /**
     * @return string
     */
    public function getCoreFolder(): string
    {
        return $this->preparePath($this->configData[self::APPLICATION]['core']);
    }

    /**
     * @return string
     */
    public function getTemplateFolder(): string
    {
        return $this->preparePath($this->configData[self::TEMPLATE]['templateFolder']);
    }

    /**
     * @return string
     */
    public function getLogFolder(): string
    {
        return $this->preparePath($this->configData[self::APPLICATION]['logFolder']);
    }

    /**
     * @return string
     */
    public function getCacheForTemplateFolder(): string
    {
        return $this->preparePath($this->configData[self::TEMPLATE]['cacheFolder']);
    }

    /**
     * @param string $path
     * @return string
     * @throws ApplicationException
     */
    private function preparePath(string $path): string
    {
        $resultPath = $path;
        $parametersCollection = [];
        while (preg_match_all('/{[a-zA-Z0-9\-_\.]*}/', $resultPath, $matches)) {
            $this->assertNotExistDuplicatePaths($path, $matches);
            foreach ($matches[0] as $match) {
                $this->assertNotExistCyclicDependency($path, $match, $parametersCollection);
                $parametersCollection[] = $match;
                $resultPath = str_replace($match, $this->searchPartPathByMatch($match), $resultPath);
            }
            $resultPath = str_replace("//", "/", $resultPath);
        }
        return $resultPath;
    }

    /**
     * @param string $match
     * @return mixed|null
     */
    private function searchPartPathByMatch(string $match)
    {
        $key = preg_replace("/[{}]/", "", $match);
        return $this->searchParameterByKey($key);
    }

    /**
     * Config constructor.
     */
    private function __construct()
    {
    }

    /**
     * @throws \Exception
     */
    private function assertCorrectJsonDecode()
    {
        if (!(json_last_error() == JSON_ERROR_NONE)) {
            throw new \Exception(sprintf("Ошибка в файле config.json: %s", json_last_error_msg()));
        }
    }

    /**
     * @param string $pathToConfigFile
     * @throws \Exception
     */
    private function assertExistsFile(string $pathToConfigFile)
    {
        if (!file_exists($pathToConfigFile)) {
            throw new \Exception(sprintf("Путь до файла с конфигурацией '%s' указан неверно!", $pathToConfigFile));
        }
    }

    /**
     * @param string $key
     * @return mixed|null
     * @throws ApplicationException
     */
    private function searchParameterByKey(string $key)
    {
        $parameter = $this->getParameterByKey($key);
        if (!$parameter) {
            throw new ApplicationException(sprintf("Параметр %s не найден!", $key));
        }
        return $parameter;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getParameterByKey(string $key)
    {
        return isset($this->configParameters[$key]) ? $this->configParameters[$key] : null;
    }

    /**
     * @param string $path
     * @param $match
     * @param $parametersCollection
     * @throws ApplicationException
     */
    private function assertNotExistCyclicDependency(string $path, $match, $parametersCollection)
    {
        if (in_array($match, $parametersCollection)) {
            throw new ApplicationException(sprintf("Циклическая зависимость в пути '%s' в файле config.json!",
                $path));
        }
    }

    /**
     * @param string $path
     * @param $matches
     * @throws ApplicationException
     */
    private function assertNotExistDuplicatePaths(string $path, $matches)
    {
        if (count($matches[0]) != count(array_unique($matches[0]))) {
            throw new ApplicationException(sprintf("Дублирование значений в пути '%s'", $path));
        }
    }
}