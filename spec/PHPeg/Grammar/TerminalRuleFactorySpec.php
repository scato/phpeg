<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\ExpressionInterface;
use PHPeg\Grammar\Tree\AnyNode;
use PHPeg\Grammar\Tree\CharacterClassNode;
use PHPeg\Grammar\Tree\LiteralNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\GrammarInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TerminalRuleFactorySpec extends ObjectBehavior
{
    function it_should_create_a_whitespace_rule()
    {
        $context = new Context();

        $this->createWhitespace()->parse(' ', $context)->isSuccess()->shouldBe(true);
        $this->createWhitespace()->parse(' ', $context)->getResult()->shouldBe(null);
        $this->createWhitespace()->parse(' ', $context)->getRest()->shouldBe('');

        $this->createWhitespace()->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->createWhitespace()->parse('foo', $context)->getResult()->shouldBe(null);
        $this->createWhitespace()->parse('foo', $context)->getRest()->shouldBe('foo');

        $this->createWhitespace()->parse("\r\n\t", $context)->isSuccess()->shouldBe(true);
        $this->createWhitespace()->parse("\r\n\t", $context)->getResult()->shouldBe(null);
        $this->createWhitespace()->parse("\r\n\t", $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_literal_rule()
    {
        $context = new Context();

        $this->createLiteral()->parse('"foo"', $context)->isSuccess()->shouldBe(true);
        $this->createLiteral()->parse('"foo"', $context)->getResult()->shouldBeLike(new LiteralNode('foo'));
        $this->createLiteral()->parse('"foo"', $context)->getRest()->shouldBe('');

        $this->createLiteral()->parse('foo', $context)->isSuccess()->shouldBe(false);

        $this->createLiteral()->parse('"\\""', $context)->getResult()->shouldBeLike(new LiteralNode('"'));
    }

    function it_should_create_an_any_rule()
    {
        $context = new Context();

        $this->createAny()->parse('.', $context)->isSuccess()->shouldBe(true);
        $this->createAny()->parse('.', $context)->getResult()->shouldBeLike(new AnyNode());
        $this->createAny()->parse('.', $context)->getRest()->shouldBeLike('');

        $this->createAny()->parse('foo', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_a_character_class_rule()
    {
        $context = new Context();

        $this->createCharacterClass()->parse('[a-z]', $context)->isSuccess()->shouldBe(true);
        $this->createCharacterClass()->parse('[a-z]', $context)->getResult()->shouldBeLike(new CharacterClassNode('a-z'));
        $this->createCharacterClass()->parse('[a-z]', $context)->getRest()->shouldBeLike('');

        $this->createCharacterClass()->parse('foo', $context)->isSuccess()->shouldBe(false);

        $this->createCharacterClass()->parse('[^\\]]', $context)->getResult()->shouldBeLike(new CharacterClassNode('^\\]'));
    }

    function it_should_create_an_identifier_rule()
    {
        $context = new Context();

        $this->createIdentifier()->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->createIdentifier()->parse('foo', $context)->getResult()->shouldBeLike('foo');
        $this->createIdentifier()->parse('foo', $context)->getRest()->shouldBe('');

        $this->createIdentifier()->parse('1foo', $context)->isSuccess()->shouldBe(false);
        $this->createIdentifier()->parse('_foo1', $context)->isSuccess()->shouldBe(true);
    }

    function it_should_create_a_rule_reference_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $grammar->getRule('Identifier')->willReturn($this->createIdentifier());

        $this->createRuleReference($grammar)->parse('foo', $context)->isSuccess()->shouldBe(true);
        $this->createRuleReference($grammar)->parse('foo', $context)->getResult()->shouldBeLike(new RuleReferenceNode('foo'));
        $this->createRuleReference($grammar)->parse('foo', $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_sub_expression_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $grammar->getRule('_')->willReturn($this->createWhitespace());
        $grammar->getRule('Identifier')->willReturn($this->createIdentifier());
        $grammar->getRule('Expression')->willReturn($this->createRuleReference($grammar));

        $this->createSubExpression($grammar)->parse('( foo )', $context)->isSuccess()->shouldBe(true);
        $this->createSubExpression($grammar)->parse('( foo )', $context)->getResult()->shouldBeLike(new RuleReferenceNode('foo'));
        $this->createSubExpression($grammar)->parse('( foo )', $context)->getRest()->shouldBe('');

        $this->createSubExpression($grammar)->parse('foo', $context)->isSuccess()->shouldBe(false);

        $this->createSubExpression($grammar)->parse('(foo)', $context)->getResult()->shouldBeLike(new RuleReferenceNode('foo'));
    }

    function it_should_create_a_terminal_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $grammar->getRule('_')->willReturn($this->createWhitespace());
        $grammar->getRule('Literal')->willReturn($this->createLiteral());
        $grammar->getRule('Any')->willReturn($this->createAny());
        $grammar->getRule('CharacterClass')->willReturn($this->createCharacterClass());
        $grammar->getRule('Identifier')->willReturn($this->createIdentifier($grammar));
        $grammar->getRule('RuleReference')->willReturn($this->createRuleReference($grammar));
        $grammar->getRule('SubExpression')->willReturn($this->createSubExpression($grammar));
        $grammar->getRule('Expression')->willReturn($this->createTerminal($grammar));

        $this->createTerminal($grammar)->parse('"foo"', $context)->getResult()->shouldBeLike(new LiteralNode('foo'));
        $this->createTerminal($grammar)->parse('.', $context)->getResult()->shouldBeLike(new AnyNode());
        $this->createTerminal($grammar)->parse('[a-z]', $context)->getResult()->shouldBeLike(new CharacterClassNode('a-z'));
        $this->createTerminal($grammar)->parse('foo', $context)->getResult()->shouldBeLike(new RuleReferenceNode('foo'));
        $this->createTerminal($grammar)->parse('( foo )', $context)->getResult()->shouldBeLike(new RuleReferenceNode('foo'));
    }
}
