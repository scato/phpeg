<?php

namespace PHPeg\Combinator;

use PHPeg\ResultInterface;

class Success implements ResultInterface
{
    private $result;
    private $rest;

    public function __construct($result, $rest)
    {
        $this->result = $result;
        $this->rest = $rest;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getRest()
    {
        return $this->rest;
    }
}
