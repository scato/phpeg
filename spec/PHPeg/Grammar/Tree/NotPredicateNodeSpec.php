<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotPredicateNodeSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression)
    {
        $this->beConstructedWith($expression);
    }

    function it_is_a_node()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\NodeInterface');
    }

    function it_has_an_expression(ExpressionInterface $expression)
    {
        $this->getExpression()->shouldBe($expression);
    }
}
