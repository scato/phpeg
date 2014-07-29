<?php

namespace PHPeg\Grammar\Tree;

class ChoiceNode extends BinaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitChoice($this);
    }
}
