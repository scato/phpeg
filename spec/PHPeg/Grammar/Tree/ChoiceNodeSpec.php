<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\NodeInterface;
use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ChoiceNodeSpec extends ObjectBehavior
{
    function let(NodeInterface $left, NodeInterface $right)
    {
        $this->beConstructedWith(array($left, $right));
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_expressions(NodeInterface $left, NodeInterface $right)
    {
        $this->getExpressions()->shouldBe(array($left, $right));
    }

    function it_should_accept_a_visitor(NodeInterface $left, NodeInterface $right, VisitorInterface $visitor)
    {
        $left->accept($visitor)->shouldBeCalled();
        $right->accept($visitor)->shouldBeCalled();
        $visitor->visitChoice($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
