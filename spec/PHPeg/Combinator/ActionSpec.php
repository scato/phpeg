<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActionSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression, ContextInterface $context)
    {
        $this->beConstructedWith($expression, 'return array($foo);');

        $context->evaluate('return array($foo);')->willReturn(array('bar'));
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_expression_succeeds_and_evaluate_the_code(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', $context)->willReturn(new Success('foo', ''));

        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->parse('foo', $context)->getResult()->shouldBe(array('bar'));
        $this->parse('foo', $context)->getRest()->shouldBe('');
    }

    function it_should_fail_if_the_expression_fails(ExpressionInterface $expression, ContextInterface $context)
    {
        $expression->parse('foo', $context)->willReturn(new Failure());

        $this->parse('foo', $context)->isSuccess()->shouldBe(false);
    }
}
