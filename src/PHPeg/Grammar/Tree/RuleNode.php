<?php

namespace PHPeg\Grammar\Tree;

class RuleNode extends AbstractNamedNode
{
    public function accept(VisitorInterface $visitor)
    {
        parent::accept($visitor);

        $visitor->visitRule($this);
    }
}
