<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;
use PHPeg\GrammarInterface;

class Grammar implements GrammarInterface
{
    private $startSymbol;
    private $rules = array();

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

        return $this->getRule($this->startSymbol)->parse($string, $context);
    }

    public function addRule($name, ExpressionInterface $rule)
    {
        $this->rules[$name] = $rule;
    }

    public function setStartSymbol($startSymbol)
    {
        if (!isset($this->rules[$startSymbol])) {
            throw new \InvalidArgumentException("No rule with name '$startSymbol' found");
        }

        $this->startSymbol = $startSymbol;
    }
}
