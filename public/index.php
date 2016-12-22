<?php
use Finance\Application;

ini_set("display_errors", true);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 900);

require __DIR__ . '/../vendor/autoload.php';

Application::startSession();
Application::launch($_SERVER, $_REQUEST, $_SESSION);


/*
$response = DataBase::getInstance()->selectQuery("SELECT COUNT(*) cnt FROM EURUSD")[0];
echo "Count rows: " . $response['cnt'];
$response = DataBase::getInstance()->selectQuery("SELECT max(end_time) max FROM EURUSD")[0];
echo "<br>Max time: " . $response['max'];
$response = DataBase::getInstance()->selectQuery("SELECT cur_bid, cur_ask FROM EURUSD WHERE end_time = (SELECT max(end_time) max FROM EURUSD)")[0];
echo "<br>Last bid: " . $response['cur_bid'];
echo "<br>Last ask: " . $response['cur_ask'];
*/
/*
$NeuralWeb = NeuralWeb::buildNeuralWeb();
$timeBefore = time();
foreach ($NeuralWeb->getNeuronLayers() as $layer) {
    //echo "Layer type: " . $layer->getType() . "<br>";
    foreach ($layer->getNeurons() as $neuron) {
        //echo "<p style='margin-left:50px;'>Neuron id: " . $neuron->getId() . "</p><br>";
        foreach ($NeuralWeb->getInputLinksTo($neuron) as $link) {
            //echo "<p style='margin-left:100px;'> Input from " . $link->getFromNeuron()->getId() . "-->>Type: " . $link->getFromNeuron()->getLayerType() . "-->>Weight: " . $link->getWeight() . "</p><br>";
        }
    }
}
$timeAfter = time();

$differenceInSeconds = -$timeBefore + $timeAfter;
echo $timeBefore . ' ' . $timeAfter . ' ' . $differenceInSeconds;
*/