<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\Grammar\TerminalRuleFactory;
use PHPeg\Grammar\Tree\OneOrMoreNode;
use PHPeg\Grammar\Tree\OptionalNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\Tree\ZeroOrMoreNode;
use PHPeg\GrammarInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UnaryRuleFactorySpec extends ObjectBehavior
{
    function let(GrammarInterface $grammar)
    {
        $terminalRuleFactory = new TerminalRuleFactory();

        $grammar->getRule('_')->willReturn($terminalRuleFactory->createWhitespace());
        $grammar->getRule('Expression')->willReturn($terminalRuleFactory->createRuleReference());
    }

    function it_should_create_a_zero_or_more_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createZeroOrMore($grammar)->parse('foo *', $context)->isSuccess()->shouldBe(true);
        $this->createZeroOrMore($grammar)->parse('foo *', $context)->getResult()->shouldBeLike(new ZeroOrMoreNode(new RuleReferenceNode('foo')));
        $this->createZeroOrMore($grammar)->parse('foo *', $context)->getRest()->shouldBe('');

        $this->createZeroOrMore($grammar)->parse('foo +', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_a_one_or_more_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createOneOrMore($grammar)->parse('foo +', $context)->isSuccess()->shouldBe(true);
        $this->createOneOrMore($grammar)->parse('foo +', $context)->getResult()->shouldBeLike(new OneOrMoreNode(new RuleReferenceNode('foo')));
        $this->createOneOrMore($grammar)->parse('foo +', $context)->getRest()->shouldBe('');

        $this->createOneOrMore($grammar)->parse('foo ?', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_an_optional_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createOptional($grammar)->parse('foo ?', $context)->isSuccess()->shouldBe(true);
        $this->createOptional($grammar)->parse('foo ?', $context)->getResult()->shouldBeLike(new OptionalNode(new RuleReferenceNode('foo')));
        $this->createOptional($grammar)->parse('foo ?', $context)->getRest()->shouldBe('');

        $this->createOptional($grammar)->parse('foo *', $context)->isSuccess()->shouldBe(false);
    }
}
