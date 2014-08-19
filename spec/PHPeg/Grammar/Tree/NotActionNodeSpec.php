<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotActionNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('return false;');
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_code()
    {
        $this->getCode()->shouldBe('return false;');
    }

    function it_should_accept_a_visitor(VisitorInterface $visitor)
    {
        $visitor->visitNotAction($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
