<?php

namespace spec\PHPeg\Combinator;

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

    function it_should_match_the_given_string()
    {
        $this->parse('foobar')->isSuccess()->shouldBe(true);
        $this->parse('foobar')->getResult()->shouldBe('foo');
        $this->parse('foobar')->getRest()->shouldBe('bar');
    }

    function it_should_not_match_other_strings()
    {
        $this->parse('bar')->isSuccess()->shouldBe(false);
    }
}
