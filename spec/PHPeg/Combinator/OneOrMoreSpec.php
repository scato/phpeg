<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OneOrMoreSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression)
    {
        $expression->parse('foofoobar')->willReturn(new Success('foo', 'foobar'));
        $expression->parse('foobar')->willReturn(new Success('foo', 'bar'));
        $expression->parse('bar')->willReturn(new Failure());

        $this->beConstructedWith($expression);
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_expression_succeeds()
    {
        $this->parse('foobar')->isSuccess()->shouldBe(true);
        $this->parse('foobar')->getResult()->shouldBe('foo');
        $this->parse('foobar')->getRest()->shouldBe('bar');
    }

    function it_should_succeed_if_the_expression_succeeds_twice()
    {
        $this->parse('foofoobar')->isSuccess()->shouldBe(true);
        $this->parse('foofoobar')->getResult()->shouldBe('foofoo');
        $this->parse('foofoobar')->getRest()->shouldBe('bar');
    }

    function it_should_fail_if_the_expression_fails()
    {
        $this->parse('bar')->isSuccess()->shouldBe(false);
    }
}
