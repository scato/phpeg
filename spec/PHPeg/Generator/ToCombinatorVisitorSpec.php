<?php

namespace spec\PHPeg\Generator;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\RuleReference;
use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\GrammarInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ToCombinatorVisitorSpec extends ObjectBehavior
{
    function let(GrammarInterface $grammar)
    {
        $this->beConstructedWith($grammar);
    }

    function it_should_create_an_action_from_an_action_node(GrammarInterface $grammar)
    {
        $actionNode = new ActionNode(new RuleReferenceNode('foo'), 'return null;');
        $action = new Action(new RuleReference($grammar->getWrappedObject(), 'foo'), 'return null;');

        $this->visitAction($actionNode);
        $this->getResult()->shouldBeLike($action);
    }

    function it_should_create_a_rule_reference_from_a_rule_reference_node(GrammarInterface $grammar)
    {
        $ruleReferenceNode = new RuleReferenceNode('foo');
        $ruleReference = new RuleReference($grammar->getWrappedObject(), 'foo');

        $this->visitRuleReference($ruleReferenceNode);
        $this->getResult()->shouldBeLike($ruleReference);
    }
}
