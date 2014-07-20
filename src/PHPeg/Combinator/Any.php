<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Any implements ExpressionInterface
{
    /**
     * @param string $string
     * @return ResultInterface
     */
    public function parse($string)
    {
        $result = substr($string, 0, 1);
        $rest = substr($string, 1);

        if ($result === false) {
            return new Failure();
        }

        return new Success($result, $rest);
    }
}
