<?php

namespace PHPeg\Grammar\Tree;

class OptionalNode extends UnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitOptional($this);
    }
}
