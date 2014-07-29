<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\NodeInterface;
use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LabelNodeSpec extends ObjectBehavior
{
    function let(NodeInterface $expression)
    {
        $this->beConstructedWith('foo', $expression);
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldBe('foo');
    }

    function it_has_an_expression(NodeInterface $expression)
    {
        $this->getExpression()->shouldBe($expression);
    }

    function it_should_accept_a_visitor(NodeInterface $expression, VisitorInterface $visitor)
    {
        $expression->accept($visitor)->shouldBeCalled();
        $visitor->visitLabel($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
