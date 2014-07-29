<?php

namespace PHPeg\Grammar\Tree;

class LabelNode extends NamedNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitLabel($this);
    }
}
