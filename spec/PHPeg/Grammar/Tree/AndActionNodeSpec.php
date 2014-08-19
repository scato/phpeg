<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AndActionNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('return true;');
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_code()
    {
        $this->getCode()->shouldBe('return true;');
    }

    function it_should_accept_a_visitor(VisitorInterface $visitor)
    {
        $visitor->visitAndAction($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
