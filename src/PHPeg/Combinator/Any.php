<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Any implements ExpressionInterface
{
    /**
     * @param string $string
     * @param ContextInterface $context
     * @return ResultInterface
     */
    public function parse($string, ContextInterface $context)
    {
        $result = substr($string, 0, 1);
        $rest = substr($string, 1);

        if ($result === false) {
            return new Failure();
        }

        return new Success($result, $rest);
    }
}
