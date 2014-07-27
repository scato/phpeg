<?php


namespace PHPeg\Grammar\Tree;


class NamedNode extends UnaryNode
{
    private $name;

    public function __construct($name, $expression)
    {
        parent::__construct($expression);

        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
