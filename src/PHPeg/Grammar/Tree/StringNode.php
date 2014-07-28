<?php


namespace PHPeg\Grammar\Tree;


abstract class StringNode implements NodeInterface
{
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function getString()
    {
        return $this->string;
    }

    public function accept(VisitorInterface $visitor)
    {
        // TODO: Implement accept() method.
    }
}
