<?php

namespace spec\PHPeg\Combinator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SuccessSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foo', 'bar');
    }

    function it_is_a_result()
    {
        $this->shouldHaveType('PHPeg\ResultInterface');
    }

    function it_should_be_a_success()
    {
        $this->isSuccess()->shouldBe(true);
    }

    function it_should_contain_the_result()
    {
        $this->getResult()->shouldBe('foo');
    }

    function it_should_contain_the_rest()
    {
        $this->getRest()->shouldBe('bar');
    }
}
