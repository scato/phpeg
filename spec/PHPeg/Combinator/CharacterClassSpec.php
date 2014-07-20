<?php

namespace spec\PHPeg\Combinator;

use PHPeg\ContextInterface;
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

    function it_should_match_a_character_that_belongs_to_the_class(ContextInterface $context)
    {
        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->parse('foo', $context)->getResult()->shouldBe('f');
        $this->parse('foo', $context)->getRest()->shouldBe('oo');
    }

    function it_should_not_match_other_characters(ContextInterface $context)
    {
        $this->parse('123', $context)->isSuccess()->shouldBe(false);
    }
}
