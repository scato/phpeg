<?php

namespace PHPeg\Grammar\Tree;

class NotPredicateNode extends UnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitNotPredicate($this);
    }
}
