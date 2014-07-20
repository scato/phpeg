<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;
use PHPeg\GrammarInterface;
use PHPeg\ResultInterface;

class RuleReference implements ExpressionInterface
{
    private $grammar;
    private $name;

    /**
     * @param GrammarInterface $grammar
     * @param string $name
     */
    public function __construct(GrammarInterface $grammar, $name)
    {
        $this->grammar = $grammar;
        $this->name = $name;
    }

    /**
     * @param string $string
     * @return ResultInterface
     */
    public function parse($string)
    {
        return $this->grammar->getRule($this->name)->parse($string);
    }
}
