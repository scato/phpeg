<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SequenceNodeSpec extends ObjectBehavior
{
    function let(ExpressionInterface $left, ExpressionInterface $right)
    {
        $this->beConstructedWith($left, $right);
    }

    function it_has_a_left_operand(ExpressionInterface $left)
    {
        $this->getLeft()->shouldBe($left);
    }

    function it_has_a_right_operand(ExpressionInterface $right)
    {
        $this->getRight()->shouldBe($right);
    }
}
