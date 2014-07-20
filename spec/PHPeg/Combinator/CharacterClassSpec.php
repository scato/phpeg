<?php

namespace spec\PHPeg\Combinator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CharacterClassSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('a-z');
    }

    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_match_a_character_that_belongs_to_the_class()
    {
        $this->parse('foo')->isSuccess()->shouldBe(true);
        $this->parse('foo')->getResult()->shouldBe('f');
        $this->parse('foo')->getRest()->shouldBe('oo');
    }

    function it_should_not_match_other_characters()
    {
        $this->parse('123')->isSuccess()->shouldBe(false);
    }
}
