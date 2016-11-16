<?php

namespace Finance\Application\NeuralWeb;

class Neuron
{
    const ACCEPTANCE_THRESHOLD = "0.7";
    const ID = 'id';
    const LAYER_TYPE = 'type';

    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $layerType;
    /**
     * @var
     */
    private $output;

    /**
     * @var NeuralLink[]
     */
    private $inputLinks;

    /**
     * neuron constructor.
     * @param int $id
     * @param string $layerType
     */
    public function __construct(int $id, string $layerType)
    {
        $this->id = $id;
        $this->layerType = $layerType;
    }

    public static function buildNeuron(array $params):self
    {
        return new self($params[self::ID], $params[self::LAYER_TYPE]);
    }

    public function go()
    {
        if ($this->limitCheck($this->summingBlock())) {
            $this->output = 1;
        } else {
            $this->output = 0;
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return float
     */
    private function summingBlock():float
    {
        $sum = 0;
        foreach ($this->inputLinks as $link) {
            $sum += $link->getFromNeuron()->getOutput() * $link->getWeight();
        }
        return $sum / $this->getInputCount();
    }


    /**
     * @param int $sum
     * @return float
     */
    private function activateFunction(int $sum):float
    {
        return 1 / (1 + exp(-$sum));
    }

    /**
     * @param int $sum
     * @return bool
     */
    private function limitCheck(int $sum):bool
    {
        if ($this->activateFunction($sum) > self::ACCEPTANCE_THRESHOLD) {
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    private function getInputCount():int
    {
        return count($this->inputLinks);
    }

    /**
     * @return string
     */
    public function getLayerType(): string
    {
        return $this->layerType;
    }
}