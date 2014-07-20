<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;
use PHPeg\GrammarInterface;

class Grammar implements GrammarInterface
{
    private $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param string $name
     * @return ExpressionInterface
     */
    public function getRule($name)
    {
        return $this->rules[$name];
    }

    /**
     * @param string $string
     * @return mixed
     */
    public function parse($string)
    {
        $context = new Context();

        return $this->getRule('start')->parse($string, $context);
    }
}
