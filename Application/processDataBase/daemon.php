<?php

namespace Finance\Application\InstaForexApi;

use Finance\Core\CoreFinance\Daemon;
use Finance\Core\DataBase;
use Finance\Core\CoreFinance\FinancialClient;

include __DIR__ . '/../../vendor/autoload.php';

$child_pid = pcntl_fork();

if ($child_pid) {
    // Выходим из родительского процесса, привязанного к консоли...
    exit(0);
}
posix_setsid();
DataBase::initDataBase("localhost", "root", "password", "finance");
$client = new FinancialClient("eurusd");
$response = $client->request();

//Prepare first record
$firstBid = $response->getBid();
$firstAsk = $response->getAsk();
$time = strtotime('+3 hours - 1 sec', time());
$firstTimeStart = date("Y-m-d H:i:s", $time);
$firstTimeEnd = date("Y-m-d H:i:s", strtotime('+1 sec', $time));


DataBase::getInstance()->insertQuery(
    "INSERT INTO `EURUSD` VALUES ('{$firstTimeStart}', '{$firstTimeEnd}', {$firstBid}, {$firstAsk})"
);

usleep(1000000);//ждем секунду
$function = function () use ($client) {
    $currency = $client->request();
    $previousRow = DataBase::getInstance()->selectQuery(
        "SELECT * FROM EURUSD WHERE end_time = (SELECT MAX(end_time) FROM EURUSD)"
    )[0];
    if (
        $previousRow['cur_bid'] != $currency->getBid()
        &&
        $previousRow['cur_ask'] != $currency->getAsk()
    ) {
        DataBase::getInstance()->insertQuery(
            "INSERT INTO EURUSD VALUES ('" . $previousRow['end_time'] . "','" . date("Y-m-d H:i:s",
                strtotime('+3 hours', time())) . "'," . $currency->getBid() . "," . $currency->getAsk() . ")"
        );

        return true;
    }

    return false;
};

$daemon = new Daemon();
$daemon->run($function);