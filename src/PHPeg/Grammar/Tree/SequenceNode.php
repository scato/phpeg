<?php

namespace PHPeg\Grammar\Tree;

class SequenceNode extends AbstractBinaryNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitSequence($this);
    }
}
