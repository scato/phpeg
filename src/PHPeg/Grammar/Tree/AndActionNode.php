<?php

namespace PHPeg\Grammar\Tree;

class AndActionNode extends AbstractActionNode
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitAndAction($this);
    }
}
