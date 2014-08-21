<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LiteralNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foo', false);
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_a_string()
    {
        $this->getString()->shouldBe('foo');
    }

    function it_has_a_case_insensitive_flag()
    {
        $this->isCaseInsensitive()->shouldBe(false);
    }

    function it_should_accept_a_visitor(VisitorInterface $visitor)
    {
        $visitor->visitLiteral($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
