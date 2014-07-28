<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SequenceNodeSpec extends ObjectBehavior
{
    function let(ExpressionInterface $left, ExpressionInterface $right)
    {
        $this->beConstructedWith(array($left, $right));
    }

    function it_has_expressions(ExpressionInterface $left, ExpressionInterface $right)
    {
        $this->getExpressions()->shouldBe(array($left, $right));
    }
}
