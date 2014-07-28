<?php


namespace PHPeg\Grammar\Tree;


abstract class BinaryNode
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
}
