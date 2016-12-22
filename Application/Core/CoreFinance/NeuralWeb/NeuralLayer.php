<?php

namespace Finance\Core\CoreFinance\NeuralWeb;

/**
 * Class NeuralLayer
 *
 * @package Finance\NeuralWeb
 */
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
        $this->type = $type;
        $this->createNeurons($neuronCount);
    }

    /**
     * @param int $count
     */
    private function createNeurons(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->neurons[] = Neuron::buildNeuron([
                Neuron::ID => $i,
                Neuron::LAYER_TYPE => $this->type]);
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

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}