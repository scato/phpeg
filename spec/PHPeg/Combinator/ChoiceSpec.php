<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ChoiceSpec extends ObjectBehavior
{
    function let(ExpressionInterface $foo, ExpressionInterface $the, ExpressionInterface $bar)
    {
        $this->beConstructedWith(array($foo, $the, $bar));
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_succeed_if_the_first_expression_succeeds(ExpressionInterface $foo, ContextInterface $context)
    {
        $foo->parse('foo', $context)->willReturn(new Success('foo', ''));

        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->parse('foo', $context)->getResult()->shouldBe('foo');
        $this->parse('foo', $context)->getRest()->shouldBe('');
    }

    function it_should_succeed_if_the_second_expression_succeeds(ExpressionInterface $foo, ExpressionInterface $the, ContextInterface $context)
    {
        $foo->parse('the', $context)->willReturn(new Failure());
        $the->parse('the', $context)->willReturn(new Success('the', ''));

        $this->parse('the', $context)->isSuccess()->shouldBe(true);
        $this->parse('the', $context)->getResult()->shouldBe('the');
        $this->parse('the', $context)->getRest()->shouldBe('');
    }

    function it_should_fail_if_all_expressions_fail(ExpressionInterface $foo, ExpressionInterface $the, ExpressionInterface $bar, ContextInterface $context)
    {
        $foo->parse('boo', $context)->willReturn(new Failure());
        $the->parse('boo', $context)->willReturn(new Failure());
        $bar->parse('boo', $context)->willReturn(new Failure());

        $this->parse('boo', $context)->isSuccess()->shouldBe(false);
    }
}
