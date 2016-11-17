<?php

namespace Finance\Application\InstaForexApi;

use Finance\Application\Database\FinanceDataBase;
use Finance\Application\Process\Daemon;

include(dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

$child_pid = pcntl_fork();
if ($child_pid) {
    exit(0);
}
posix_setsid();

$response = new PrepareResponse("EURUSD");

//Prepare first record
$firstBid = $response->sendRequest()->getBid();
$firstAsk = $response->sendRequest()->getAsk();
$firstTimeStart = date("Y-m-d H:i:s", strtotime('+2 hours - 1 sec', time()));
$firstTimeEnd = date("Y-m-d H:i:s", strtotime('+2 hours', time()));


$timeDB = new FinanceDataBase();
$timeDB->makeQuery(
    "INSERT INTO `financeTime` VALUES ('" . $firstTimeStart . "','" . $firstTimeEnd . "'," . $firstBid . "," . $firstAsk . ")"
);

sleep(1);//ждем секунду
$function = function () use ($response, $timeDB) {
    $financialData = $response->sendRequest();
    $previousRow = $timeDB->getOneRow(
        "SELECT * FROM financeTime WHERE end_time = (SELECT MAX(end_time) FROM financeTime)"
    );
    if ((floatval($previousRow['bid']) + floatval($previousRow['ask'])) != (floatval($financialData->getBid()) + floatval($financialData->getAsk()))) {
        $timeDB->makeQuery(
            "INSERT INTO `financeTime` VALUES ('" . $previousRow['end_time'] . "','" . date("Y-m-d H:i:s", strtotime('+2 hours', time())) . "'," . $financialData->getBid() . "," . $financialData->getAsk() . ")"
        );
    }
};

$daemon = new Daemon();
$daemon->run($function);