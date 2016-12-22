<?php

namespace Finance\Core\CoreFinance;

use Exception;

class Daemon
{
    /**
     * @var bool
     */
    private $stop = false;
    /**
     * @var int
     */
    private $sleep = 1000000;

    /**
     * Daemon constructor.
     *
     * @param string $file
     * @param int $sleep
     */
    public function __construct($file = '/../Process/tmp/daemon.pid', $sleep = 1000000)
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
    public function isDaemonActive(string $pid_file): bool
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
            usleep($this->sleep);
        }
    }

    /**
     * @param $file
     * @throws Exception
     */
    private function assertExistsDaemon($file)
    {
        if ($this->isDaemonActive($file)) {
            throw new Exception("Daemon is already exist!");
        }
    }

    /**
     * @param $file
     */
    private function savePID($file)
    {
        file_put_contents(__DIR__ . $file, getmypid());
    }

}