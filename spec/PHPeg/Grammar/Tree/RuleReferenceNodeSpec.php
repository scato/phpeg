<?php

namespace spec\PHPeg\Grammar\Tree;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RuleReferenceNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foo');
    }

    function it_has_a_string()
    {
        $this->getName()->shouldBe('foo');
    }
}
