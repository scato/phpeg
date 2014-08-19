<?php

namespace PHPeg\Grammar\Tree;

class NotPredicateNode extends AbstractUnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitNotPredicate($this);
    }
}
