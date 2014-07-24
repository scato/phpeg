<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
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

    function it_should_succeed_if_both_expressions_succeed(ExpressionInterface $left, ExpressionInterface $right, ContextInterface $context)
    {
        $left->parse('foobar', $context)->willReturn(new Success('foo', 'bar'));
        $right->parse('bar', $context)->willReturn(new Success('bar', ''));

        $this->parse('foobar', $context)->isSuccess()->shouldBe(true);
        $this->parse('foobar', $context)->getResult()->shouldBe('foobar');
        $this->parse('foobar', $context)->getRest()->shouldBe('');
    }

    function it_should_fail_if_the_left_expression_fails(ExpressionInterface $left, ContextInterface $context)
    {
        $left->parse('bar', $context)->willReturn(new Failure());

        $this->parse('bar', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_fail_if_the_right_expression_fails(ExpressionInterface $left, ExpressionInterface $right, ContextInterface $context)
    {
        $left->parse('foofoo', $context)->willReturn(new Success('foo', 'foo'));
        $right->parse('foo', $context)->willReturn(new Failure());

        $this->parse('foofoo', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_result_in_an_array_if_one_of_the_results_is_not_a_string(ExpressionInterface $left, ExpressionInterface $right, ContextInterface $context)
    {
        $left->parse('foobar', $context)->willReturn(new Success('foo', 'bar'));
        $right->parse('bar', $context)->willReturn(new Success(null, ''));

        $this->parse('foobar', $context)->getResult()->shouldBe(array('foo', null));
    }
}
