<?php

namespace PHPeg\Grammar\Tree;

class LiteralNode extends StringNode
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitLiteral($this);
    }
}
