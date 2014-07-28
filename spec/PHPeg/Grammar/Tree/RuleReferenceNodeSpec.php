<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RuleReferenceNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foo');
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_a_string()
    {
        $this->getName()->shouldBe('foo');
    }

    function it_should_accept_a_visitor(VisitorInterface $visitor)
    {
        $visitor->visitRuleReference($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
