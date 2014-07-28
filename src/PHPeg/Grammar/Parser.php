<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\ExpressionInterface;

class Parser
{
    private $start;

    public function __construct(ExpressionInterface $start)
    {
        $this->start = $start;
    }

    public function parse($string)
    {
        $context = new Context();

        $result = $this->start->parse($string, $context);

        if (!$result->isSuccess()) {
            throw new \InvalidArgumentException("Could not parse '$string'");
        }

        if ($result->getRest() !== '') {
            throw new \InvalidArgumentException("Unexpected input: '{$result->getRest()}'");
        }

        return $result->getResult();
    }
}
