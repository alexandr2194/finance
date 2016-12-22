<?php
namespace Finance\Core;

use Finance\Core\Exceptions\ApplicationException;

/**
 * Class ContentManager
 * @package Finance\Core
 */
class FileManager
{
    /**
     * @var string
     */
    private $lastFileName;

    /**
     * @param string $fileName
     * @param string $folder
     * @param string $data
     */
    public function saveFile(string $fileName, string $folder, string $data)
    {
        $this->createFolder($folder);
        $this->lastFileName = $fileName;
        $file = fopen($folder . "/" . $fileName, "w");
        fwrite($file, $data);
        fclose($file);
    }

    /**
     * @param string $fileName
     * @param string $folder
     * @param string $data
     */
    public function appendFile(string $fileName, string $folder, string $data)
    {
        $this->createFolder($folder);
        $this->lastFileName = $fileName;
        $file = fopen($folder . "/" . $fileName, "a+");
        fwrite($file, $data);
        fclose($file);
    }

    /**
     * @return string
     */
    public function getLastFileName(): string
    {
        return $this->lastFileName;
    }

    /**
     * @param string $folder
     * @return array
     */
    public function getAllFilesInFolder(string $folder): array
    {
        if (!file_exists($folder)) {
            return [];
        }
        $files = scandir($folder, 1);
        return preg_grep("/^debug_\d{4}\-\d{2}\-\d{2}_\d{2}:\d{2}:\d{2}/", $files);
    }

    /**
     * @param string $pathToFile
     * @return string
     * @throws \Exception
     */
    public function loadFileContent(string $pathToFile): string
    {
        if (file_exists($pathToFile)) {
            return file_get_contents($pathToFile);
        }
        throw new ApplicationException("Вы запрашиваете несуществующий файл!");
    }

    /**
     * @param string $folder
     */
    private function createFolder(string $folder)
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
    }
}