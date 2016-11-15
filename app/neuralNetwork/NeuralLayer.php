<?php
namespace Finance\NeuralNetwork;


class NeuralLayer
{
    const NEURON_COUNT = 'neuronCount';
    const TYPE = 'type';
    const INPUT = 'input';
    const OUTPUT = 'output';
    const HIDDEN = 'hidden';
    /**
     * @var Neuron[]
     */
    private $neurons;
    /**
     * @var string
     */
    private $type;

    /**
     * NeuralLayer constructor.
     * @param int $neuronCount
     * @param string $type
     */
    public function __construct(int $neuronCount, string $type)
    {
        $this->createNeurons($neuronCount);
        $this->type = $type;
    }

    /**
     * @param int $count
     */
    private function createNeurons(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->neurons[] = Neuron::buildNeuron([Neuron::ID => $i]);
        }
    }

    /**
     * @param array $params
     * @return NeuralLayer
     */
    public static function buildLayer(array $params):self
    {
        return new self($params[self::NEURON_COUNT], $params[self::TYPE]);
    }

    /**
     * @return Neuron[]
     */
    public function getNeurons(): array
    {
        return $this->neurons;
    }
}