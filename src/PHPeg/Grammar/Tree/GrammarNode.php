<?php

namespace PHPeg\Grammar\Tree;

class GrammarNode
{
    private $name;
    private $startSymbol;
    private $rules;

    public function __construct($name, $startSymbol, array $rules)
    {
        $this->name = $name;
        $this->startSymbol = $startSymbol;
        $this->rules = $rules;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStartSymbol()
    {
        return $this->startSymbol;
    }

    public function getRules()
    {
        return $this->rules;
    }
}
