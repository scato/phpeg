<?php


namespace PHPeg\Grammar\Tree;


abstract class AbstractActionNode implements NodeInterface
{
    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }
}
