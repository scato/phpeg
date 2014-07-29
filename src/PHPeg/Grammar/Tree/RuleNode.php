<?php

namespace PHPeg\Grammar\Tree;

class RuleNode extends NamedNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitRule($this);
    }
}
