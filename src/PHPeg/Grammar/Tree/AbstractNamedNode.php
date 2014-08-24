<?php


namespace PHPeg\Grammar\Tree;


class AbstractNamedNode extends AbstractUnaryNode
{
    private $name;

    public function __construct($name, NodeInterface $expression)
    {
        parent::__construct($expression);

        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
