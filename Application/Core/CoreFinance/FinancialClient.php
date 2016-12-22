<?php
namespace Finance\Core\CoreFinance;

use GuzzleHttp\Client;

/**
 * Class FinancialClient
 *
 * @package Finance\CoreFinance
 */
class FinancialClient
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
     * @return Currency
     */
    public function request(): Currency
    {
        $response = (new Client())->request(
            'POST',
            self::FOREX_URL . 'api/quotesTick?m=json&q=' . rawurlencode($this->symbol)
        );
        $response = json_decode($response->getBody(), true)[0];

        return Currency::build($response);
    }


}