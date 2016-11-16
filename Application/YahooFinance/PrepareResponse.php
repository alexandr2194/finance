<?php
namespace Finance\Application\YahooFinance;

use Exception;

class PrepareResponse
{
    const YAHOO_URL = 'https://quotes.instaforex.com/api/quotesTick?m=json&q=';
    const ERROR = 'error';
    /**
     * @var string
     */
    private $symbol;

    /**
     * prepareResponse constructor.
     * @param string $symbol
     */
    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return FinancialData
     */
    public function sendRequest():FinancialData
    {
        $response = $this->getResponse();
        $this->assertErrorsNotExists($response);
        return $this->createFinancialData($response);
    }

    /**
     * @return string
     */
    private function buildRequest():string
    {
        $this->assertNotEmptySymbol();
        return self::YAHOO_URL . $this->symbol;
    }

    /**
     * @return array
     */
    private function getResponse():array
    {
        return json_decode(
            file_get_contents(
                $this->buildRequest()
            ),
            true
        )[0];
    }

    /**
     * @param $response
     * @return FinancialData
     */
    private function createFinancialData($response):FinancialData
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
     * @param $response
     * @throws Exception
     */
    private function assertErrorsNotExists($response)
    {
        if (array_key_exists(self::ERROR, $response)) {
            throw new Exception($response[self::ERROR]);
        }
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