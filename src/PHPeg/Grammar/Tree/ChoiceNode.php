<?php

namespace PHPeg\Grammar\Tree;

class ChoiceNode extends AbstractBinaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitChoice($this);
    }
}
