<?php

namespace spec\PHPeg\Combinator;

use PHPeg\ContextInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LiteralSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foo');
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_match_the_given_string(ContextInterface $context)
    {
        $this->parse('foobar', $context)->isSuccess()->shouldBe(true);
        $this->parse('foobar', $context)->getResult()->shouldBe('foo');
        $this->parse('foobar', $context)->getRest()->shouldBe('bar');
    }

    function it_should_not_match_other_strings(ContextInterface $context)
    {
        $this->parse('bar', $context)->isSuccess()->shouldBe(false);
    }
}
