<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\ExpressionInterface;
use PHPeg\GrammarInterface;

class Parser
{
    private $grammar;

    public function __construct(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    public function parse($string)
    {
        $context = new Context();

        $result = $this->grammar->parse($string, $context);

        if (!$result->isSuccess()) {
            throw new \InvalidArgumentException("Could not parse '$string'");
        }

        if ($result->getRest() !== '') {
            throw new \InvalidArgumentException("Unexpected input: '{$result->getRest()}'");
        }

        return $result->getResult();
    }
}
