<?php

namespace PHPeg\Grammar\Tree;

class ActionNode extends UnaryNode implements NodeInterface
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

    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitAction($this);
    }
}
