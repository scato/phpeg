<?php

namespace PHPeg\Grammar\Tree;

class MatchedStringNode extends AbstractUnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitMatchedString($this);
    }
}
