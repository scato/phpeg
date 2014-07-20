<?php

namespace spec\PHPeg\Combinator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnySpec extends ObjectBehavior
{
    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_match_any_character()
    {
        $this->parse('foo')->isSuccess()->shouldBe(true);
        $this->parse('foo')->getResult()->shouldBe('f');
        $this->parse('foo')->getRest()->shouldBe('oo');
    }

    function it_should_not_match_end_of_file()
    {
        $this->parse('')->isSuccess()->shouldBe(false);
    }
}
