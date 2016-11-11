<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 11.11.16
 * Time: 15:20
 */

namespace Finance\YahooFinance;
use Finance\Application\Daemon;

include (__DIR__ . '/../process/Daemon.php');
include (__DIR__ . '/PrepareResponse.php');
include (__DIR__ . '/FinancialData.php');



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
$daemon->run($function);