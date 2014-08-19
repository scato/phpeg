<?php


namespace PHPeg\Grammar\Tree;


abstract class AbstractUnaryNode implements NodeInterface
{
    private $expression;

    public function __construct(NodeInterface $expression)
    {
        $this->expression = $expression;
    }

    public function getExpression()
    {
        return $this->expression;
    }

    public function accept(VisitorInterface $visitor)
    {
        $this->expression->accept($visitor);
    }
}
