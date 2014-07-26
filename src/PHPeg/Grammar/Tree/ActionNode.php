<?php

namespace PHPeg\Grammar\Tree;

class ActionNode extends UnaryNode
{
    private $code;

    function __construct($expression, $code)
    {
        parent::__construct($expression);

        $this->code = $code;
    }


    public function getCode()
    {
        return $this->code;
    }
}
