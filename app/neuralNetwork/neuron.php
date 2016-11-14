<?php
/**
 * Created by PhpStorm.
 * User: aleksandr.i
 * Date: 14.11.16
 * Time: 11:02
 */

namespace Finance\NeuralNetwork;


use Exception;

class Neuron
{
    const ACCEPTANCE_THRESHOLD = "0.7";

    //TODO
    const REJECTION_THRESHOLD = "0.3";

    /**
     * @var int
     */
    private $id;
    /**
     * @var array
     */
    private $inputs;

    /**
     * @var array
     */
    private $weights;

    /**
     * @var
     */
    private $output;

    /**
     * neuron constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
        $this->initWeight();
    }

    /**
     *
     */
    public function init()
    {
        $this->initWeight();
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
     * @param Neuron[] $neurons
     */
    public function transferTo(array $neurons)
    {
        foreach ($neurons as $neuron) {
            $neuron->receiveInput($this->inputs);
        }
    }

    /**
     * @param $inputs
     */
    public function receiveInput($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * @param Neuron $neuron
     */
    public function connectWith(Neuron $neuron)
    {
        $inputs[] = $neuron->getId();
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
        foreach ($this->inputs as $input) {
            foreach ($this->weights as $weight) {
                $sum += $input * $weight;
            }
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
     *
     */
    private function initWeight()
    {
        for ($i = 0; $i < $this->getInputCount(); $i++) {
            $this->weights[$i] = rand(0, 50);
        }
    }

    /**
     * @return int
     */
    private function getInputCount():int
    {
        return count($this->inputs);
    }

    /**
     * @param Neuron $neuron
     * @param int $offset
     * @throws Exception
     */
    public function changeWeightInput(Neuron $neuron, int $offset)
    {
        foreach ($this->inputs as $input => $key) {
            if ($input == $neuron->getId()) {
                $this->weights[$key] += $offset;
            }
        }
        throw new Exception("Neurons %s and %s not associated!", $this->id, $neuron->getId());
    }
}