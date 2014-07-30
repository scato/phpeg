<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ExpressionInterface;
use PHPeg\Grammar\Tree\AndPredicateNode;
use PHPeg\Grammar\Tree\MatchedStringNode;
use PHPeg\Grammar\Tree\NotPredicateNode;
use PHPeg\Grammar\Tree\OneOrMoreNode;
use PHPeg\Grammar\Tree\OptionalNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\Tree\ZeroOrMoreNode;
use PHPeg\GrammarInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UnaryRuleFactorySpec extends ObjectBehavior
{
    function let(GrammarInterface $grammar, ExpressionInterface $whitespace, ExpressionInterface $terminal)
    {
        $whitespace->parse(Argument::that(function ($string) {
            return preg_match('/^ /', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(' ', strval(substr($args[0], 1)));
        });

        $whitespace->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('', $args[0]);
        });

        $terminal->parse(Argument::that(function ($string) {
            return preg_match('/^foo/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(new RuleReferenceNode('foo'), strval(substr($args[0], 3)));
        });

        $terminal->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function () {
            return new Failure();
        });

        $grammar->getRule('_')->willReturn($whitespace);
        $grammar->getRule('Terminal')->willReturn($terminal);

        $grammar->getRule('AndPredicate')->willReturn($this->createAndPredicate($grammar));
        $grammar->getRule('NotPredicate')->willReturn($this->createNotPredicate($grammar));
        $grammar->getRule('ZeroOrMore')->willReturn($this->createZeroOrMore($grammar));
        $grammar->getRule('OneOrMore')->willReturn($this->createOneOrMore($grammar));
        $grammar->getRule('Optional')->willReturn($this->createOptional($grammar));
        $grammar->getRule('MatchedString')->willReturn($this->createMatchedString($grammar));
        $grammar->getRule('Repetition')->willReturn($this->createRepetition($grammar));
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

    function it_should_create_a_repetition_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createRepetition($grammar)->parse('& foo +', $context)->isSuccess()->shouldBe(false);

        $this->createRepetition($grammar)->parse('& foo', $context)->isSuccess()->shouldBe(false);

        $this->createRepetition($grammar)->parse('foo +', $context)->isSuccess()->shouldBe(true);
        $this->createRepetition($grammar)->parse('foo +', $context)->getRest()->shouldBe('');

        $this->createRepetition($grammar)->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->createRepetition($grammar)->parse('foo', $context)->getRest()->shouldBe('');
    }

    function it_should_create_an_and_predicate_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createAndPredicate($grammar)->parse('& foo', $context)->isSuccess()->shouldBe(true);
        $this->createAndPredicate($grammar)->parse('& foo', $context)->getResult()->shouldBeLike(new AndPredicateNode(new RuleReferenceNode('foo')));
        $this->createAndPredicate($grammar)->parse('& foo', $context)->getRest()->shouldBe('');

        $this->createAndPredicate($grammar)->parse('! foo', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_a_not_predicate_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createNotPredicate($grammar)->parse('! foo', $context)->isSuccess()->shouldBe(true);
        $this->createNotPredicate($grammar)->parse('! foo', $context)->getResult()->shouldBeLike(new NotPredicateNode(new RuleReferenceNode('foo')));
        $this->createNotPredicate($grammar)->parse('! foo', $context)->getRest()->shouldBe('');

        $this->createNotPredicate($grammar)->parse('$ foo', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_a_matched_string_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createMatchedString($grammar)->parse('$ foo', $context)->isSuccess()->shouldBe(true);
        $this->createMatchedString($grammar)->parse('$ foo', $context)->getResult()->shouldBeLike(new MatchedStringNode(new RuleReferenceNode('foo')));
        $this->createMatchedString($grammar)->parse('$ foo', $context)->getRest()->shouldBe('');

        $this->createMatchedString($grammar)->parse('& foo', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_a_predicate_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createPredicate($grammar)->parse('& foo +', $context)->isSuccess()->shouldBe(true);
        $this->createPredicate($grammar)->parse('& foo +', $context)->getResult()->shouldBeLike(new AndPredicateNode(new OneOrMoreNode(new RuleReferenceNode('foo'))));
        $this->createPredicate($grammar)->parse('& foo +', $context)->getRest()->shouldBe('');

        $this->createPredicate($grammar)->parse('& foo', $context)->isSuccess()->shouldBe(true);
        $this->createPredicate($grammar)->parse('& foo', $context)->getRest()->shouldBe('');

        $this->createPredicate($grammar)->parse('foo +', $context)->isSuccess()->shouldBe(true);
        $this->createPredicate($grammar)->parse('foo +', $context)->getRest()->shouldBe('');
    }
}
