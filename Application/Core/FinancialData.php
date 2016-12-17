<?php

namespace Application\Core;

/**
 * Class FinancialData
 *
 * @package Finance\Application\Core
 */
class FinancialData
{
    const DIGITS = 'digits';
    const ASK = 'ask';
    const BID = 'bid';
    const CHANGE = 'change';
    const SYMBOL = 'symbol';
    const LAST_TIME = 'lasttime';
    const CHANGE_24_H = 'change24h';

    /**
     * @var integer
     */
    private $digits;
    /**
     * @var float
     */
    private $ask;
    /**
     * @var float
     */
    private $bid;
    /**
     * @var float
     */
    private $change;
    /**
     * @var string
     */
    private $symbol;
    /**
     * @var integer
     */
    private $lastTime;
    /**
     * @var float
     */
    private $change24h;

    /**
     * FinancialData constructor.
     *
     * @param $digits
     * @param $ask
     * @param $bid
     * @param $change
     * @param $symbol
     * @param $lastTime
     * @param $change24h
     */
    public function __construct(
        int $digits,
        float $ask,
        float $bid,
        float $change,
        string $symbol,
        int $lastTime,
        float $change24h
    ) {
        $this->digits = $digits;
        $this->ask = $ask;
        $this->bid = $bid;
        $this->change = $change;
        $this->symbol = $symbol;
        $this->lastTime = $lastTime;
        $this->change24h = $change24h;
    }

    /**
     * @return float
     */
    public function getAsk(): float
    {
        return $this->ask;
    }

    /**
     * @return float
     */
    public function getBid(): float
    {
        return $this->bid;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return int
     */
    public function getLastTime(): int
    {
        return $this->lastTime;
    }

}