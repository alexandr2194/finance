<?php
namespace Finance\Core;

/**
 * Class Model
 * @package Finance\Core
 */
class Model
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}