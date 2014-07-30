<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Grammar\BinaryRuleFactory;
use PHPeg\Grammar\GrammarRuleFactory;
use PHPeg\Grammar\TerminalRuleFactory;
use PHPeg\Grammar\Tree\GrammarNode;
use PHPeg\Grammar\Tree\RuleNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\UnaryRuleFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new TerminalRuleFactory(),
            new UnaryRuleFactory(),
            new BinaryRuleFactory(),
            new GrammarRuleFactory()
        );
    }

    function it_should_parse_a_grammar()
    {
        $definition = ' grammar Foo { start foo = the; the = bar; } ';

        $tree = new GrammarNode('Foo', 'foo', array(
            new RuleNode('foo', new RuleReferenceNode('the')),
            new RuleNode('the', new RuleReferenceNode('bar')),
        ));

        $this->createParser()->parse($definition)->shouldBeLike($tree);
    }
}
