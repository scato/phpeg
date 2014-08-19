<?php


namespace PHPeg\Grammar\Tree;


class CharacterClassNode extends AbstractStringNode
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitCharacterClass($this);
    }
}
