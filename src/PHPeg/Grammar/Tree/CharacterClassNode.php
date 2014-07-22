<?php


namespace PHPeg\Grammar\Tree;


class CharacterClassNode
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
}
