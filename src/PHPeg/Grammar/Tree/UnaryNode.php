<?php


namespace PHPeg\Grammar\Tree;


abstract class UnaryNode implements NodeInterface
{
    private $expression;

    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function getExpression()
    {
        return $this->expression;
    }

    public function accept(VisitorInterface $visitor)
    {
        // TODO: Implement accept() method.
    }
}
