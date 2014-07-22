<?php

namespace PHPeg\Grammar\Tree;

class LiteralNode
{
    private $string;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    public function getString()
    {
        return $this->string;
    }
}
