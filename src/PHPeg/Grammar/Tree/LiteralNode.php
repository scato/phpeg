<?php

namespace PHPeg\Grammar\Tree;

class LiteralNode extends AbstractStringNode
{
    private $caseInsensitive;

    public function __construct($string, $caseInsensitive)
    {
        parent::__construct($string);

        $this->caseInsensitive = $caseInsensitive;
    }

    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitLiteral($this);
    }

    public function isCaseInsensitive()
    {
        return $this->caseInsensitive;
    }
}
