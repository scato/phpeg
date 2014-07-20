<?php

namespace spec\PHPeg\Combinator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContextSpec extends ObjectBehavior
{
    function it_is_a_context()
    {
        $this->shouldHaveType('PHPeg\ContextInterface');
    }

    function it_contains_values()
    {
        $this->set('name', 'foo');
        $this->get('name')->shouldBe('foo');
    }
}
