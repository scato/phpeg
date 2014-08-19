<?php

namespace PHPeg\Grammar\Tree;

class NotActionNode extends AbstractActionNode
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitNotAction($this);
    }
}
