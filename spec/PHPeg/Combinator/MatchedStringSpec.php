<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatchedStringSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foobar', $context)->willReturn(new Success(array('f', 'o', 'o'), 'bar'));
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
        $this->parse('foobar', $context)->getRest()->shouldBe('bar');
    }

    function it_should_result_in_the_string_that_was_matched(ExpressionInterface $expression, ContextInterface $context)
    {
        $this->parse('foobar', $context)->getResult()->shouldBe('foo');
    }

    function it_should_fail_if_the_expression_fails(ContextInterface $context)
    {
        $this->parse('bar', $context)->isSuccess()->shouldBe(false);
    }
}
