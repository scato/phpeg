<?php


namespace PHPeg\Grammar\Tree;


class CharacterClassNode extends StringNode
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitCharacterClass($this);
    }
}
