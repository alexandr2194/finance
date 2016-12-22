<?php

namespace Finance\Core\CoreFinance\NeuralWeb;

/**
 * Class NeuralWeb
 *
 * @package Finance\NeuralWeb
 */
class NeuralWeb
{
    const NEURON_LAYERS_COUNT = 'neuronLayersCount';

    /**
     * @var NeuralLayer[]
     */
    private $neuronLayers;

    /**
     * @var NeuralLink[]
     */
    private $neuronLinks;

    /**
     * NeuralNetwork constructor.
     */
    public function __construct()
    {
        $params = $this->loadConfig();
        $this->createLayers($params);
        $this->createLinks();
    }

    /**
     * @param array $params
     */
    public function createLayers(array $params)
    {
        foreach ($params as $type => $param) {
            if ($this->checkLayerType($type)) {
                $this->addLayer($param[NeuralLayer::NEURON_COUNT], $type);
            }
        }
    }

    /**
     * @return NeuralWeb
     */
    public static function buildNeuralWeb():self
    {
        return new self();
    }

    /**
     * @return NeuralLink[]
     */
    public function getNeuronLinks(): array
    {
        return $this->neuronLinks;
    }

    /**
     * @return NeuralLayer[]
     */
    public function getNeuronLayers(): array
    {
        return $this->neuronLayers;
    }

    /**
     * @param Neuron $neuron
     * @return NeuralLink[]
     */
    public function getInputLinksTo(Neuron $neuron):array
    {
        $result = array();
        foreach ($this->neuronLinks as $link) {
            if ($link->isCurrentNeuronInputLink($neuron)) {
                $result[] = $link;
            }
        }
        return $result;
    }

    /**
     * @param string $pathToConfig
     * @return array
     */
    private function loadConfig(string $pathToConfig = '/config/params.json'):array
    {
        $this->assertExistsFile($pathToConfig);
        return json_decode(file_get_contents(dirname(__FILE__) . $pathToConfig), true);
    }

    /**
     * @param int $neuronCount
     * @param string $type
     */
    private function addLayer(int $neuronCount, string $type)
    {
        $this->neuronLayers[] = NeuralLayer::buildLayer([
            NeuralLayer::NEURON_COUNT => $neuronCount,
            NeuralLayer::TYPE => $type
        ]);
    }

    /**
     * @param $type
     * @return bool
     */
    private function checkLayerType($type):bool
    {
        return in_array($type, [NeuralLayer::INPUT, NeuralLayer::OUTPUT, NeuralLayer::HIDDEN]);
    }

    /**
     * @param $pathToConfig
     * @throws \Exception
     */
    private function assertExistsFile($pathToConfig)
    {
        if (!file_exists(dirname(__FILE__) . $pathToConfig)) {
            throw new \Exception("Config file not found");
        }
    }

    private function createLinks()
    {
        for ($i = 0; $i < count($this->neuronLayers) - 1; $i++) {
            foreach ($this->neuronLayers[$i]->getNeurons() as $firstLayerNeuron) {
                foreach ($this->neuronLayers[$i + 1]->getNeurons() as $secondLayerNeuron) {
                    $this->addLink($firstLayerNeuron, $secondLayerNeuron);
                }
            }
        }
    }

    /**
     * @param $firstLayerNeuron
     * @param $secondLayerNeuron
     */
    private function addLink($firstLayerNeuron, $secondLayerNeuron)
    {
        $this->neuronLinks[] = NeuralLink::buildLink([
            NeuralLink::NEURON_FROM => $firstLayerNeuron,
            NeuralLink::NEURON_TO => $secondLayerNeuron,
            NeuralLink::WEIGHT => rand(0, 20)
        ]);
    }
}
