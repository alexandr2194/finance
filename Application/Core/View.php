<?php
namespace Finance\Core;

use Finance\Core\Exceptions\ApplicationException;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class View
 *
 * @package Finance\Views
 */
class View
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * View constructor.
     */
    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(Config::getInstance()->getTemplateFolder());
        $this->twig = new Twig_Environment($loader, [
            'cache' => Config::getInstance()->getCacheForTemplateFolder(),
            'debug' => true
        ]);
    }

    /**
     * @param string $templateFileName
     * @param array $data
     */
    public function generate(string $templateFileName, array $data)
    {
        $this->assertExistFile(__DIR__ . '/../Templates/' . $templateFileName);
        echo $this->twig->render($templateFileName, $data);
    }

    private function assertExistFile(string $templateFileName)
    {
        if (!file_exists($templateFileName)) {
            throw new ApplicationException(sprintf("Шаблон %s не найден!", $templateFileName));
        }
    }
}