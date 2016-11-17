<?php

use Finance\Application\Database\FinanceDataBase;
use Finance\Application\InstaForexApi\PrepareResponse;
use Finance\Application\WebSocket\WebSocket;

ini_set("display_errors", true);
include(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

$app = require_once __DIR__ . '/../Application/application.php';
$app->run();

$eurUsd = new prepareResponse("EURUSD");
$financialData = $eurUsd->sendRequest();
echo $financialData->getBid() . "  ask: " .$financialData->getAsk(). "<br>";

echo phpinfo();
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
