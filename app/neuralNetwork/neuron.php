<?php
namespace Finance\NeuralNetwork;


use Exception;

class Neuron
{
    const ACCEPTANCE_THRESHOLD = "0.7";
    const ID = 'id';

    /**
     * @var int
     */
    private $id;

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
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function buildNeuron(array $params):self
    {
        return new self($params[self::ID]);
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
}