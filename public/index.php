<?php

use Finance\Application\Database\FinanceDataBase;
use Finance\Application\YahooFinance\PrepareResponse;

ini_set("display_errors", true);
include(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

$app = require_once __DIR__ . '/../Application/application.php';
$app->run();

$eurUsd = new prepareResponse("EURUSD");
$financialData = $eurUsd->sendRequest();
echo $financialData->getBid() . "<br>";
echo "Последние изменения цен: " . date("d.m.Y   H:i:s", $financialData->getLastTime()) . "<br>";
echo "Сейчас: " . date("d.m.Y   H:i:s", strtotime('+2 hours',time())) . "<br>";
echo "Сейчас: " . date("d.m.Y   H:i:s", strtotime('+2 hours -1 sec',time())) . "<br>";


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
$timeDB = new FinanceDataBase();
insertRow(
    $timeDB,
    $firstTimeStart,
    $firstTimeEnd,
    $firstBid,
    $firstAsk
);


/*$NeuralWeb = NeuralWeb::buildNeuralWeb();
foreach ($NeuralWeb->getNeuronLayers() as $layer) {
    echo "Layer type: " . $layer->getType() . "<br>";
    foreach ($layer->getNeurons() as $neuron) {
        echo "<p style='margin-left:50px;'>Neuron id: " . $neuron->getId() . "</p><br>";
        foreach ($NeuralWeb->getInputLinksTo($neuron) as $link) {
            echo "<p style='margin-left:100px;'> Input from " . $link->getFromNeuron()->getId() . "-->>Type: " . $link->getFromNeuron()->getLayerType() . "-->>Weight: " . $link->getWeight() . "</p><br>";
        }
    }
}
*/


/**
 * @param mixed $value
 */
function var_dump_ex($value)
{
    echo "<pre>";
    echo var_dump($value);
    echo "</pre>";
}