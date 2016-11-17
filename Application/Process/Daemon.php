<?php

namespace Finance\Application\Process;

use Exception;

class Daemon
{
    /**
     * @var bool
     */
    protected $stop = false;

    /**
     * @var int
     */
    protected $sleep = 1;

    /**
     * Daemon constructor.
     * @param string $file
     * @param int $sleep
     */
    public function __construct($file = '/tmp/daemon.pid', $sleep = 1)
    {
        $this->assertExistsDaemon($file);
        $this->sleep = $sleep;
        pcntl_signal(SIGTERM, [$this, 'signalHandler']);
        $this->savePID($file);
    }

    /**
     * @param string $signal
     */
    public function signalHandler(string $signal)
    {
        switch ($signal) {
            case SIGTERM:
                $this->stop = true;
                break;
        }
    }

    /**
     * @param string $pid_file
     * @return bool
     * @throws Exception
     */
    public function isDaemonActive(string $pid_file):bool
    {
        if (is_file($pid_file)) {
            $pid = file_get_contents($pid_file);
            if (posix_kill($pid, 0)) {
                return true;
            } else {
                if (!unlink($pid_file)) {
                    throw new Exception("Error!");
                }
            }
        }
        return false;
    }

    /**
     * @param $func
     */
    public function run($func)
    {
        while (!$this->stop) {
            do {
                $resp = $func();
                if (!empty($resp)) {
                    break;
                }
            } while (true);
            sleep($this->sleep);
        }
    }

    /**
     * @param $file
     * @throws Exception
     */
    private function assertExistsDaemon($file)
    {
        if ($this->isDaemonActive($file)) {
            throw new Exception("Daemon is already exsist!");
        }
    }

    /**
     * @param $file
     */
    private function savePID($file)
    {
        file_put_contents(dirname(__FILE__) . $file, getmypid());
    }

}