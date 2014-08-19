<?php

namespace PHPeg\Grammar\Tree;

class ZeroOrMoreNode extends AbstractUnaryNode implements NodeInterface
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitZeroOrMore($this);
    }
}
