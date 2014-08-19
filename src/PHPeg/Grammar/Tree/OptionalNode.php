<?php

namespace PHPeg\Grammar\Tree;

class OptionalNode extends AbstractUnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitOptional($this);
    }
}
