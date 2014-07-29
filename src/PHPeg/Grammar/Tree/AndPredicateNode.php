<?php

namespace PHPeg\Grammar\Tree;

class AndPredicateNode extends UnaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitAndPredicate($this);
    }
}
