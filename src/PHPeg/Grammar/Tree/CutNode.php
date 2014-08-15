<?php

namespace PHPeg\Grammar\Tree;

class CutNode implements NodeInterface
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitCut($this);
    }
} 
