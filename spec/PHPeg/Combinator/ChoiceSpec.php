<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ChoiceSpec extends ObjectBehavior
{
    function let(ExpressionInterface $left, ExpressionInterface $right)
    {
        $this->beConstructedWith($left, $right);
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_left_expression_succeeds(ExpressionInterface $left, ContextInterface $context)
    {
        $left->parse('foo', $context)->willReturn(new Success('foo', ''));

        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->parse('foo', $context)->getResult()->shouldBe('foo');
        $this->parse('foo', $context)->getRest()->shouldBe('');
    }

    function it_should_succeed_if_the_right_expression_succeeds(ExpressionInterface $left, ExpressionInterface $right, ContextInterface $context)
    {
        $left->parse('bar', $context)->willReturn(new Failure());
        $right->parse('bar', $context)->willReturn(new Success('bar', ''));

        $this->parse('bar', $context)->isSuccess()->shouldBe(true);
        $this->parse('bar', $context)->getResult()->shouldBe('bar');
        $this->parse('bar', $context)->getRest()->shouldBe('');
    }

    function it_should_fail_if_both_expressions_fail(ExpressionInterface $left, ExpressionInterface $right, ContextInterface $context)
    {
        $left->parse('boo', $context)->willReturn(new Failure());
        $right->parse('boo', $context)->willReturn(new Failure());

        $this->parse('boo', $context)->isSuccess()->shouldBe(false);
    }
}
