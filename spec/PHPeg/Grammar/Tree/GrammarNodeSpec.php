<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\RuleNode;
use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrammarNodeSpec extends ObjectBehavior
{
    function let(RuleNode $rule)
    {
        $this->beConstructedWith('Foo', 'Bar', array($rule));
        $this->setNamespace('Acme\\Factory');
        $this->setImports(array('Acme\\FactoryInterface'));
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_a_namespace()
    {
        $this->getNamespace()->shouldBe('Acme\\Factory');
    }

    function it_has_imports()
    {
        $this->getImports()->shouldBe(array('Acme\\FactoryInterface'));
    }

    function it_has_a_name()
    {
        $this->getName()->shouldBe('Foo');
    }

    function it_has_a_start_symbol()
    {
        $this->getStartSymbol()->shouldBe('Bar');
    }

    function it_has_rules(RuleNode $rule)
    {
        $this->getRules()->shouldBe(array($rule));
    }

    function it_has_a_length()
    {
        $this->getLength()->shouldBe(1);
    }

    function it_should_accept_a_visitor(RuleNode $rule, VisitorInterface $visitor)
    {
        $rule->accept($visitor)->shouldBeCalled();
        $visitor->visitGrammar($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
