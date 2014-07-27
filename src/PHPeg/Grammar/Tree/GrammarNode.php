<?php

namespace PHPeg\Grammar\Tree;

class GrammarNode
{
    private $name;
    private $rules;

    public function __construct($name, array $rules)
    {
        $this->name = $name;
        $this->rules = $rules;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRules()
    {
        return $this->rules;
    }
}
