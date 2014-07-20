<?php

namespace PHPeg\Combinator;

use PHPeg\ResultInterface;

class Failure implements ResultInterface
{
    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return false;
    }

    /**
     * @return mixed
     * @throws \LogicException
     */
    public function getResult()
    {
        throw new \LogicException('Failure does not contain result');
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function getRest()
    {
        throw new \LogicException('Failure does not contain rest');
    }
}
