<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LabelSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression)
    {
        $this->beConstructedWith('name', $expression);
    }
    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_expression_succeeds(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', $context)->willReturn(new Success('foo', ''));

        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->parse('foo', $context)->getResult()->shouldBe('foo');
        $this->parse('foo', $context)->getRest()->shouldBe('');
    }

    function it_should_fail_if_the_expression_fails(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', $context)->willReturn(new Failure());

        $this->parse('foo', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_write_the_result_to_the_context(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', $context)->willReturn(new Success('foo', ''));
        $context->set('name', 'foo')->shouldBeCalled();

        $this->parse('foo', $context);
    }
}
