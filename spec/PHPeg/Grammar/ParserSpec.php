<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ExpressionInterface;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function let(ExpressionInterface $start)
    {
        $this->beConstructedWith($start);
    }

    function it_should_return_the_result_from_the_start_symbol(ExpressionInterface $start)
    {
        $start->parse('foo', Argument::type('\PHPeg\ContextInterface'))->willReturn(new Success(new RuleReferenceNode('foo'), ''));

        $this->parse('foo')->shouldBeLike(new RuleReferenceNode('foo'));
    }

    function it_should_fail_if_there_is_input_left(ExpressionInterface $start)
    {
        $start->parse('foo^', Argument::type('\PHPeg\ContextInterface'))->willReturn(new Success(new RuleReferenceNode('foo'), '^'));

        $this->shouldThrow('\InvalidArgumentException')->duringParse('foo^');
    }

    function it_should_fail_if_the_input_is_invalid(ExpressionInterface $start)
    {
        $start->parse('^foo', Argument::type('\PHPeg\ContextInterface'))->willReturn(new Failure());

        $this->shouldThrow('\InvalidArgumentException')->duringParse('^foo');
    }
}