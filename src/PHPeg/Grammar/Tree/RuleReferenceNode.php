<?php


namespace PHPeg\Grammar\Tree;


class RuleReferenceNode implements NodeInterface
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitRuleReference($this);
    }
}
