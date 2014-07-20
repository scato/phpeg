<?php

namespace spec\PHPeg\Combinator;

use PHPeg\ContextInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnySpec extends ObjectBehavior
{
    function it_is_an_expression()
    {
        $this->shouldHaveType('PHPeg\ExpressionInterface');
    }

    function it_should_match_any_character(ContextInterface $context)
    {
        $this->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->parse('foo', $context)->getResult()->shouldBe('f');
        $this->parse('foo', $context)->getRest()->shouldBe('oo');
    }

    function it_should_not_match_end_of_file(ContextInterface $context)
    {
        $this->parse('', $context)->isSuccess()->shouldBe(false);
    }
}
