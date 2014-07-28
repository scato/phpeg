<?php

namespace PHPeg\Grammar\Tree;

interface VisitorInterface
{
    public function visitAction(ActionNode $node);
    public function visitRuleReference(RuleReferenceNode $node);
}
