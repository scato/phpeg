<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
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

    function it_should_succeed_if_the_left_expression_succeeds(ExpressionInterface $left)
    {
        $left->parse('foo')->willReturn(new Success('foo', ''));

        $this->parse('foo')->isSuccess()->shouldBe(true);
        $this->parse('foo')->getResult()->shouldBe('foo');
        $this->parse('foo')->getRest()->shouldBe('');
    }

    function it_should_succeed_if_the_right_expression_succeeds(ExpressionInterface $left, ExpressionInterface $right)
    {
        $left->parse('bar')->willReturn(new Failure());
        $right->parse('bar')->willReturn(new Success('bar', ''));

        $this->parse('bar')->isSuccess()->shouldBe(true);
        $this->parse('bar')->getResult()->shouldBe('bar');
        $this->parse('bar')->getRest()->shouldBe('');
    }

    function it_should_fail_if_both_expressions_fail(ExpressionInterface $left, ExpressionInterface $right)
    {
        $left->parse('boo')->willReturn(new Failure());
        $right->parse('boo')->willReturn(new Failure());

        $this->parse('boo')->isSuccess()->shouldBe(false);
    }
}
