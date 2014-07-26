<?php


namespace PHPeg\Grammar\Tree;


abstract class BinaryNode
{
    private $left;
    private $right;

    function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function getRight()
    {
        return $this->right;
    }
}
