<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 10.11.16
 * Time: 12:58
 */

use Finance\YahooFinance\prepareResponse;

include(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

$app = require_once __DIR__ . '/../app/application.php';
$app->run();
$eurUsd = new prepareResponse("EURUSD");
$financialData = $eurUsd->sendRequest();
echo $financialData->getBid() . "<br>";
