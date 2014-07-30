<?php

namespace PHPeg\Grammar\Tree;

class MatchedStringNode extends UnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitMatchedString($this);
    }
}
