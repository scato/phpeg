<?php

namespace PHPeg\Grammar\Tree;

class LiteralNode extends AbstractStringNode
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitLiteral($this);
    }
}
