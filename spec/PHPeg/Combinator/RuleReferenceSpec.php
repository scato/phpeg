<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\GrammarInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RuleReferenceSpec extends ObjectBehavior
{
    function let(GrammarInterface $grammar, ExpressionInterface $expression)
    {
        $this->beConstructedWith($grammar, 'name');

        $grammar->getRule('name')->willReturn($expression);
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_rule_succeeds(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', Argument::type('\PHPeg\ContextInterface'))->willReturn(new Success('foo', 'bar'));

        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
    }

    function it_should_fail_if_the_rule_fails(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', Argument::type('\PHPeg\ContextInterface'))->willReturn(new Failure());

        $this->parse('foo', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_use_its_own_context(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', $context)->shouldNotBeCalled();
        $expression->parse('foo', Argument::type('\PHPeg\ContextInterface'))->willReturn(new Success('foo', 'bar'));

        $this->parse('foo', $context);
    }
}
