<?php

namespace spec\PHPeg\Grammar\Tree;

use PHPeg\Grammar\Tree\RuleNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrammarNodeSpec extends ObjectBehavior
{
    function let(RuleNode $rule)
    {
        $this->beConstructedWith('foo', array($rule));
    }

    function it_has_a_name()
    {
        $this->getName()->shouldBe('foo');
    }

    function it_has_rules(RuleNode $rule)
    {
        $this->getRules()->shouldBe(array($rule));
    }
}
