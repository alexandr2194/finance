<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 11.11.16
 * Time: 15:20
 */

namespace Finance\YahooFinance;
use Finance\Application\Daemon;
include (__DIR__ . 'PrepareResponse.php');


$child_pid = pcntl_fork();
if( $child_pid ) {
    // Выходим из родительского процесса, привязанного к консоли...
    exit(0);
}

posix_setsid();

$response = new PrepareResponse("EURUSD");

$function = function () use ($response) {
    $financialData = $response->sendRequest();
    return $financialData->getBid();
};

$daemon = new Daemon();

fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);

$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen('/dev/null', 'wb');
$STDERR = fopen('/dev/null', 'wb');

$daemon->run($function);