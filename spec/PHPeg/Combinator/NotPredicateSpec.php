<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotPredicateSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foobar', $context)->willReturn(new Success('foo', 'bar'));
        $expression->parse('bar', $context)->willReturn(new Failure());

        $this->beConstructedWith($expression);
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_expression_fails_without_consuming_anything(ContextInterface $context)
    {
        $this->parse('bar', $context)->isSuccess()->shouldBe(true);
        $this->parse('bar', $context)->getResult()->shouldBe(null);
        $this->parse('bar', $context)->getRest()->shouldBe('bar');
    }

    function it_should_fail_if_the_expression_succeeds(ContextInterface $context)
    {
        $this->parse('foobar', $context)->isSuccess()->shouldBe(false);
    }
}
