<?php

namespace spec\PHPeg\Grammar\Tree;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnyNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPeg\Grammar\Tree\AnyNode');
    }
}
