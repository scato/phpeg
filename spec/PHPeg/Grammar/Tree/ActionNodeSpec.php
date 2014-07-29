<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\NodeInterface;
use PHPeg\Grammar\Tree\VisitorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActionNodeSpec extends ObjectBehavior
{
    function let(NodeInterface $expression)
    {
        $this->beConstructedWith($expression, 'return null;');
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_an_expression(NodeInterface $expression)
    {
        $this->getExpression()->shouldBe($expression);
    }

    function it_has_code()
    {
        $this->getCode()->shouldBe('return null;');
    }

    function it_should_accept_a_visitor(NodeInterface $expression, VisitorInterface $visitor)
    {
        $expression->accept($visitor)->shouldBeCalled();
        $visitor->visitAction($this)->shouldBeCalled();

        $this->accept($visitor);
    }
}
