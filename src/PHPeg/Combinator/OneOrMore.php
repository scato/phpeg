<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;

class OneOrMore extends Proxy
{
    public function __construct(ExpressionInterface $expression)
    {
        parent::__construct(new Sequence($expression, new ZeroOrMore($expression)));
    }
}
