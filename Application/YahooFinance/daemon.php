<?php

namespace Finance\Application\YahooFinance;

use Finance\Application\Database\FinanceDataBase;
use Finance\Application\Process\Daemon;

$timeDB = new FinanceDataBase();
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

/**
 * @param $timeDB
 * @param $firstTimeStart
 * @param $firstTimeEnd
 * @param $firstBid
 * @param $firstAsk
 */
function insertRow(FinanceDataBase $timeDB, $firstTimeStart, $firstTimeEnd, $firstBid, $firstAsk)
{
    $timeDB->makeQuery(
        "INSERT INTO `financeTime` VALUES ('" . $firstTimeStart . "','" . $firstTimeEnd . "'," . $firstBid . "," . $firstAsk . ")"
    );
}

insertRow(
    $timeDB,
    $firstTimeStart,
    $firstTimeEnd,
    $firstBid,
    $firstAsk
);
sleep(1);//ждем секунду
$function = function () use ($response, $timeDB) {
    $financialData = $response->sendRequest();
    $nowDate = date("Y-m-d H:i:s", strtotime('+2 hours', time()));
    $previousRow = $timeDB->getOneRow(
        "SELECT * FROM financeTime WHERE end_time = (SELECT MAX(end_time) FROM financeTime)"
    );
    insertRow(
        $timeDB,
        $previousRow['end_time'],
        $nowDate,
        $financialData->getBid(),
        $financialData->getAsk()
    );
};

$daemon = new Daemon();
$daemon->run($function);