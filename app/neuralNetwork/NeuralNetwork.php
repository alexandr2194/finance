<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 14.11.16
 * Time: 15:48
 */

namespace Finance\NeuralNetwork;


class NeuralNetwork
{
    /**
     * @var NeuralLayer
     */
    private $neuronLayers;

    /**
     * @var int
     */
    private $neuronLayersCount;

    /**
     * NeuralNetwork constructor.
     * @param int $neuronLayersCount
     */
    public function __construct(int $neuronLayersCount)
    {
        $this->neuronLayersCount = $neuronLayersCount;
    }

    public function setInputLayer(int $neuronCount)
    {

    }
}


class NeuralLayer
{
    /**
     * @var Neuron[]
     */
    private $neurons;

    private $neuronCount;

    /**
     * NeuralLayer constructor.
     * @param int $neuronCount
     */
    public function __construct(int $neuronCount)
    {
        $this->neuronCount = $neuronCount;
    }

    public function createNeuron($id)
    {
        $neuron = new Neuron($id);
        $neuron->init();
        $this->neurons[] = $neuron;
    }
}

class inputLayer extends NeuralLayer
{
    public function __construct($neuronCount)
    {
        parent::__construct($neuronCount);
    }


}