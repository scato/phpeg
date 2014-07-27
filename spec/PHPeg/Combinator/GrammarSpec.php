<?php

namespace spec\PHPeg\Combinator;

use PHPeg\Combinator\Success;
use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrammarSpec extends ObjectBehavior
{
    function let(ExpressionInterface $start, ExpressionInterface $name)
    {
        $this->beConstructedWith(array(
            'start' => $start,
            'name' => $name,
        ));
    }

    function it_should_contain_rules(ExpressionInterface $name)
    {
        $this->getRule('name')->shouldBe($name);
    }

    function it_should_fail_on_non_existent_rules()
    {
        $this->shouldThrow('\InvalidArgumentException')->duringGetRule('foo');
    }

    function it_should_start_parsing_at_the_start_symbol(ExpressionInterface $start, ContextInterface $context)
    {
        $start->parse('foo', Argument::type('\PHPeg\ContextInterface'))->shouldBeCalled()->willReturn(new Success('foo', ''));

        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->parse('foo', $context)->getResult()->shouldBe('foo');
        $this->parse('foo', $context)->getRest()->shouldBe('');
    }
}
