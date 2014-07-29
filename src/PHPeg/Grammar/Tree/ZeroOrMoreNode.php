<?php

namespace PHPeg\Grammar\Tree;

class ZeroOrMoreNode extends UnaryNode implements NodeInterface
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitZeroOrMore($this);
    }
}
