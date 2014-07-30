<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\ExpressionInterface;
use PHPeg\GrammarInterface;
use PHPeg\ParserInterface;

class Parser implements ParserInterface
{
    private $grammar;

    public function __construct(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    public function parse($string)
    {
        $result = $this->grammar->parse($string);

        if (!$result->isSuccess()) {
            throw new \InvalidArgumentException("Could not parse '$string'");
        }

        if ($result->getRest() !== '') {
            throw new \InvalidArgumentException("Unexpected input: '{$result->getRest()}'");
        }

        return $result->getResult();
    }
}
