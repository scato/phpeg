<?php

namespace spec\PHPeg\Grammar\Tree;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CharacterClassNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('a-z');
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_a_string()
    {
        $this->getString()->shouldBe('a-z');
    }
}
