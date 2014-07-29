<?php


namespace PHPeg\Grammar\Tree;


class AnyNode implements NodeInterface
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitAny($this);
    }
}
