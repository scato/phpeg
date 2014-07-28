<?php


namespace PHPeg\Grammar\Tree;


abstract class BinaryNode implements NodeInterface
{
    private $expressions;

    function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    public function getExpressions()
    {
        return $this->expressions;
    }

    public function accept(VisitorInterface $visitor)
    {
        // TODO: Implement accept() method.
    }
}
