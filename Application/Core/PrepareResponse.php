<?php
namespace Application\Core;

use Exception;
use GuzzleHttp\Client;

class PrepareResponse
{
    const FOREX_URL = 'https://quotes.instaforex.com/';
    const ERROR = 'error';

    /**
     * @var string
     */
    private $symbol;

    /**
     * prepareResponse constructor.
     *
     * @param string $symbol
     */
    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return FinancialData
     */
    public function sendRequest()
    {
        $response = (new Client())->request(
            'POST',
            self::FOREX_URL . 'api/quotesTick?m=json&q=' . rawurlencode($this->symbol)
        );
        $response->getStatusCode();
        echo $response->getBody();
        var_dump($response);
        //return $this->buildFinancialData($response);
    }

    /**
     * @param $response
     * @return FinancialData
     */
    private function buildFinancialData($response): FinancialData
    {
        return new FinancialData(
            $response[FinancialData::DIGITS],
            $response[FinancialData::ASK],
            $response[FinancialData::BID],
            $response[FinancialData::CHANGE],
            $response[FinancialData::SYMBOL],
            $response[FinancialData::LAST_TIME],
            $response[FinancialData::CHANGE_24_H]);
    }

    /**
     * @throws Exception
     */
    private function assertNotEmptySymbol()
    {
        if (empty($this->symbol)) {
            throw new Exception("Установлен пустой символ");
        }
    }
}