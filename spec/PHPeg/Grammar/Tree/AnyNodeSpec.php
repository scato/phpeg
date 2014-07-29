<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnyNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\AnyNode');
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_should_accept_a_visitor(VisitorInterface $visitor)
    {
        $visitor->visitAny($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
