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
     * @throws \InvalidArgumentException
     */
    public function getRule($name)
    {
        if (!isset($this->rules[$name])) {
            throw new \InvalidArgumentException("No rule with name '$name' found");
        }

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
