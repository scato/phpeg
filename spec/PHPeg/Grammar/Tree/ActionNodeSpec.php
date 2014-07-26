<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\ExpressionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActionNodeSpec extends ObjectBehavior
{
    function let(ExpressionInterface $expression)
    {
        $this->beConstructedWith($expression, 'return null;');
    }

    function it_has_an_expression(ExpressionInterface $expression)
    {
        $this->getExpression()->shouldBe($expression);
    }

    function it_has_code()
    {
        $this->getCode()->shouldBe('return null;');
    }
}
