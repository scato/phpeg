<?php

namespace PHPeg\Grammar\Tree;

class AndPredicateNode extends AbstractUnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitAndPredicate($this);
    }
}
