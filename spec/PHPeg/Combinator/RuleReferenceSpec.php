<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
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

    function it_should_succeed_if_the_rule_succeeds(ExpressionInterface $expression)
    {
        $expression->parse('foo')->willReturn(new Success('foo', 'bar'));

        $this->parse('foo')->isSuccess()->shouldBe(true);
    }

    function it_should_fail_if_the_rule_fails(ExpressionInterface $expression)
    {
        $expression->parse('foo')->willReturn(new Failure());

        $this->parse('foo')->isSuccess()->shouldBe(false);
    }
}