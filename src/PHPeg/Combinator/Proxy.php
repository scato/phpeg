<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

abstract class Proxy implements ExpressionInterface
{
    private $expression;

    protected function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @param string $string
     * @return ResultInterface
     */
    public function parse($string)
    {
        return $this->expression->parse($string);
    }
}
