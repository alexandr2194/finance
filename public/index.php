<?php

use Application\Core\DataBase;
use Application\Core\PrepareResponse;

require __DIR__ . '/../vendor/autoload.php';

ini_set("display_errors", true);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 900);





$eurUsd = new PrepareResponse("#bitcoin");
$financialData = $eurUsd->sendRequest();
exit;
echo "Bid: " . $financialData->getBid() . "; Ask: " . $financialData->getAsk() . "<br>";

$t = DataBase::getInstance()->query("SELECT * FROM EURUSD");
echo var_dump($t);
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