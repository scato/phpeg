<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OneOrMoreSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foofoobar', $context)->willReturn(new Success('foo', 'foobar'));
        $expression->parse('foobar', $context)->willReturn(new Success('foo', 'bar'));
        $expression->parse('bar', $context)->willReturn(new Failure());

        $this->beConstructedWith($expression);
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_expression_succeeds(ContextInterface $context)
    {
        $this->parse('foobar', $context)->isSuccess()->shouldBe(true);
        $this->parse('foobar', $context)->getResult()->shouldBe('foo');
        $this->parse('foobar', $context)->getRest()->shouldBe('bar');
    }

    function it_should_succeed_if_the_expression_succeeds_twice(ContextInterface $context)
    {
        $this->parse('foofoobar', $context)->isSuccess()->shouldBe(true);
        $this->parse('foofoobar', $context)->getResult()->shouldBe('foofoo');
        $this->parse('foofoobar', $context)->getRest()->shouldBe('bar');
    }

    function it_should_fail_if_the_expression_fails(ContextInterface $context)
    {
        $this->parse('bar', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_result_in_an_array_the_results_are_not_strings(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foofoobar', $context)->willReturn(new Success(null, 'foobar'));
        $expression->parse('foobar', $context)->willReturn(new Success(null, 'bar'));
        $expression->parse('bar', $context)->willReturn(new Failure());

        $this->parse('foofoobar', $context)->getResult()->shouldBe(array(null, null));
    }
}
