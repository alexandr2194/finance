<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 10.11.16
 * Time: 13:37
 */

namespace Finance\Application;

use Exception;

class app
{
    /**
     * @var array
     */
    private $config;


    private function configInitialization()
    {
        $this->assertExistsConfigFile();
        $this->configLoad();
    }

    /**
     *
     */
    public function run()
    {
        $this->configInitialization();
    }

    /**
     *
     */
    private function assertExistsConfigFile()
    {
        if (!file_exists(dirname(__FILE__) . "/config/config.json")) {
            throw new Exception("Configuration file not found! Please check availability \"config.json\" file in the \"Project\\app\\config\" directory");
        }
    }

    private function configLoad()
    {
        $this->config = json_decode(
            file_get_contents(dirname(__FILE__) . "/config/config.json"),
            true
        );
    }
}