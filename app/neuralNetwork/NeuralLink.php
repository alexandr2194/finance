<?php
namespace Finance\NeuralNetwork;


class NeuralLink
{
    const NEURON_FROM = 'neuronFrom';
    const NEURON_TO = 'neuronTo';
    const WEIGHT = 'weight';
    /**
     * @var Neuron
     */
    private $fromNeuron;
    /**
     * @var Neuron
     */
    private $toNeuron;
    /**
     * @var int
     */
    private $weight;

    /**
     * NeuralLink constructor.
     * @param Neuron $from
     * @param Neuron $to
     * @param int $weight
     */
    public function __construct(Neuron $from, Neuron $to, int $weight)
    {
        $this->fromNeuron = $from;
        $this->toNeuron = $to;
        $this->weight = $weight;
    }

    /**
     * @param array $params
     * @return NeuralLink
     */
    public static function buildLink(array $params):self
    {
        return new self($params[self::NEURON_FROM], $params[self::NEURON_TO], $params[self::WEIGHT]);
    }

    /**
     * @return Neuron
     */
    public function getFromNeuron(): Neuron
    {
        return $this->fromNeuron;
    }

    /**
     * @return Neuron
     */
    public function getToNeuron(): Neuron
    {
        return $this->toNeuron;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param Neuron $neuron
     * @return bool
     */
    public function isCurrentNeuronInputLink(Neuron $neuron):bool
    {
        if ($this->toNeuron == $neuron) {
            return true;
        }
        return false;
    }

    /**
     * @param Neuron $neuron
     * @return bool
     */
    public function isCurrentNeuronOutputLink(Neuron $neuron):bool
    {
        if ($this->toNeuron == $neuron) {
            return true;
        }
        return false;
    }
}