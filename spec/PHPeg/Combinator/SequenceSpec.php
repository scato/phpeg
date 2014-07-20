<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SequenceSpec extends ObjectBehavior
{
    function let(ExpressionInterface $left, ExpressionInterface $right)
    {
        $this->beConstructedWith($left, $right);
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_both_expressions_succeed(ExpressionInterface $left, ExpressionInterface $right)
    {
        $left->parse('foobar')->willReturn(new Success('foo', 'bar'));
        $right->parse('bar')->willReturn(new Success('bar', ''));

        $this->parse('foobar')->isSuccess()->shouldBe(true);
        $this->parse('foobar')->getResult()->shouldBe('foobar');
        $this->parse('foobar')->getRest()->shouldBe('');
    }

    function it_should_fail_if_the_left_expression_fails(ExpressionInterface $left)
    {
        $left->parse('bar')->willReturn(new Failure());

        $this->parse('bar')->isSuccess()->shouldBe(false);
    }

    function it_should_fail_if_the_right_expression_fails(ExpressionInterface $left, ExpressionInterface $right)
    {
        $left->parse('foofoo')->willReturn(new Success('foo', 'foo'));
        $right->parse('foo')->willReturn(new Failure());

        $this->parse('foofoo')->isSuccess()->shouldBe(false);
    }
}
