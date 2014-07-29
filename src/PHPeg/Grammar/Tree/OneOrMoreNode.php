<?php

namespace PHPeg\Grammar\Tree;

class OneOrMoreNode extends UnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitOneOrMore($this);
    }
}
