<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 11.11.16
 * Time: 15:28
 */

namespace Finance\Application;

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
        if ($this->isDaemonActive($file)) {
            echo "Daemon is already exsist!\n";
            exit(0);
        }
        $this->sleep = $sleep;
        pcntl_signal(SIGTERM, [$this, 'signalHandler']);
        file_put_contents($file, getmypid());
    }

    /**
     * @param $signal
     */
    public function signalHandler($signal)
    {
        switch ($signal) {
            case SIGTERM:
                $this->stop = true;
                break;
        }
    }

    /**
     * @param $pid_file
     * @return bool
     */
    public function isDaemonActive($pid_file)
    {
        if (is_file($pid_file)) {
            $pid = file_get_contents($pid_file);
            if (posix_kill($pid, 0)) {
                return true;
            } else {
                if (!unlink($pid_file)) {
                    exit(-1);
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
                    file_put_contents('tmp/text.txt',$resp);
                    break;
                }
            } while (true);
            sleep($this->sleep);
        }
    }

}