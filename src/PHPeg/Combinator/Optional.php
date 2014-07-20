<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Optional implements ExpressionInterface
{
    private $expression;

    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @param string $string
     * @return ResultInterface
     */
    public function parse($string)
    {
        $result = $this->expression->parse($string);

        if ($result->isSuccess()) {
            return $result;
        }

        return new Success('', $string);
    }
}
