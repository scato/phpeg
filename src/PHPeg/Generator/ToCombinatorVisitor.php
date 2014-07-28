<?php

namespace PHPeg\Generator;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\RuleReference;
use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\Tree\VisitorInterface;
use PHPeg\GrammarInterface;

class ToCombinatorVisitor implements VisitorInterface
{
    private $grammar;
    private $result;

    public function __construct(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    public function visitRuleReference(RuleReferenceNode $node)
    {
        $this->result = new RuleReference($this->grammar, $node->getName());
    }

    public function getResult()
    {
        return $this->result;
    }

    public function visitAction(ActionNode $node)
    {
        $node->getExpression()->accept($this);
        $expression = $this->result;

        $this->result = new Action($expression, $node->getCode());
    }
}
