<?php

namespace spec\PHPeg\Combinator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FailureSpec extends ObjectBehavior
{
    function it_is_a_result()
    {
        $this->shouldHaveType('PHPeg\ResultInterface');
    }

    function it_should_not_be_a_success()
    {
        $this->isSuccess()->shouldBe(false);
    }

    function it_should_not_contain_a_result()
    {
        $this->shouldThrow('\LogicException')->duringGetResult();
    }

    function it_should_not_contain_the_rest()
    {
        $this->shouldThrow('\LogicException')->duringGetRest();
    }
}
