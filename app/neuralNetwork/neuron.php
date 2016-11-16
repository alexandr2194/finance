<?php
namespace Finance\NeuralNetwork;


use Exception;

class Neuron
{
    const ACCEPTANCE_THRESHOLD = "0.7";
    const ID = 'id';
    const LAYER_TYPE = 'type';

    public static $currentID = 0;
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
    public function __construct(string $layerType)
    {
        $this->id = self::$currentID;
        $this->layerType = $layerType;
        self::$currentID++;
    }

    /**
     * @param array $params
     * @return Neuron
     */
    public static function buildNeuron(array $params):self
    {
        return new self($params[self::LAYER_TYPE]);
    }

    public function go()
    {
        $sum = $this->summingBlock();
        if ($this->limitCheck($sum)) {
            $this->output = 1;
        } else {
            $this->output = 0;
        }
    }


    public function transferInputToOutput()
    {
        $this->assertInputLayer();
        $this->output = $this->inputLinks[0];
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
        $activate = $this->activateFunction($sum);
        if ($activate > self::ACCEPTANCE_THRESHOLD) {
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

    private function assertInputLayer():void
    {
        if (count($this->inputLinks) != 1) {
            throw new Exception("Не может быть больше 1 входа у input layer!");
        }
    }
}