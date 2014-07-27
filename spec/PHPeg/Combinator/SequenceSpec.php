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
    function let(ExpressionInterface $foo, ExpressionInterface $the, ExpressionInterface $bar)
    {
        $this->beConstructedWith(array($foo, $the, $bar));
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_all_expressions_succeed(ExpressionInterface $foo, ExpressionInterface $the, ExpressionInterface $bar, ContextInterface $context)
    {
        $foo->parse('foothebar', $context)->willReturn(new Success('foo', 'thebar'));
        $the->parse('thebar', $context)->willReturn(new Success('the', 'bar'));
        $bar->parse('bar', $context)->willReturn(new Success('bar', ''));

        $this->parse('foothebar', $context)->isSuccess()->shouldBe(true);
        $this->parse('foothebar', $context)->getResult()->shouldBe(array('foo', 'the', 'bar'));
        $this->parse('foothebar', $context)->getRest()->shouldBe('');
    }

    function it_should_fail_if_the_first_expression_fails(ExpressionInterface $foo, ContextInterface $context)
    {
        $foo->parse('bar', $context)->willReturn(new Failure());

        $this->parse('bar', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_fail_if_the_second_expression_fails(ExpressionInterface $foo, ExpressionInterface $the, ContextInterface $context)
    {
        $foo->parse('foobar', $context)->willReturn(new Success('foo', 'bar'));
        $the->parse('bar', $context)->willReturn(new Failure());

        $this->parse('foobar', $context)->isSuccess()->shouldBe(false);
    }
}
