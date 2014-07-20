<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;

class Optional extends Proxy
{
    public function __construct(ExpressionInterface $expression)
    {
        parent::__construct(new Choice($expression, new Literal('')));
    }
}
