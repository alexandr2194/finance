<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 10.11.16
 * Time: 12:58
 */


use Finance\NeuralNetwork\NeuralWeb;
use Finance\YahooFinance\PrepareResponse;

include(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

$app = require_once __DIR__ . '/../app/application.php';
$app->run();
$eurUsd = new prepareResponse("EURUSD");
$financialData = $eurUsd->sendRequest();
echo $financialData->getBid() . "<br>";

$neuralWeb = NeuralWeb::buildNeuralWeb();
foreach ($neuralWeb->getNeuronLayers() as $layer) {
    echo "Layer type: " . $layer->getType() . "<br>";
    foreach ($layer->getNeurons() as $neuron) {
        echo "<p style='margin-left:50px;'>Neuron id: " . $neuron->getId() . "</p><br>";
        foreach ($neuralWeb->getInputLinksTo($neuron) as $link) {
            echo "<p style='margin-left:100px;'> Input from " . $link->getFromNeuron()->getId() . "-->>Type: " . $link->getFromNeuron()->getLayerType() . "-->>Weight: " . $link->getWeight() . "</p><br>";
        }
    }
}
