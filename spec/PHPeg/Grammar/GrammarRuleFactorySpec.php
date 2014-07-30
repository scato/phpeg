<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ExpressionInterface;
use PHPeg\Grammar\Tree\GrammarNode;
use PHPeg\Grammar\Tree\RuleNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\GrammarInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrammarRuleFactorySpec extends ObjectBehavior
{
    function let(GrammarInterface $grammar, ExpressionInterface $whitespace, ExpressionInterface $identifier, ExpressionInterface $expression)
    {
        $whitespace->parse(Argument::that(function ($string) {
            return preg_match('/^ /', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(' ', strval(substr($args[0], 1)));
        });

        $whitespace->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('', $args[0]);
        });

        $identifier->parse(Argument::that(function ($string) {
            return preg_match('/^foo/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('foo', strval(substr($args[0], 3)));
        });

        $identifier->parse(Argument::that(function ($string) {
            return preg_match('/^the/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('the', strval(substr($args[0], 3)));
        });

        $identifier->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function () {
            return new Failure();
        });

        $expression->parse(Argument::that(function ($string) {
            return preg_match('/^the/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(new RuleReferenceNode('the'), strval(substr($args[0], 3)));
        });

        $expression->parse(Argument::that(function ($string) {
            return preg_match('/^bar/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(new RuleReferenceNode('bar'), strval(substr($args[0], 3)));
        });

        $expression->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function () {
            return new Failure();
        });

        $grammar->getRule('_')->willReturn($whitespace);
        $grammar->getRule('Identifier')->willReturn($identifier);
        $grammar->getRule('Expression')->willReturn($expression);

        $grammar->getRule('Rule')->willReturn($this->createRule($grammar));
        $grammar->getRule('Grammar')->willReturn($this->createGrammar($grammar));
    }

    function it_should_create_a_rule_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createRule($grammar)->parse('foo = bar;', $context)->isSuccess()->shouldBe(true);
        $this->createRule($grammar)->parse('foo = bar;', $context)->getResult()->shouldBeLike(new RuleNode('foo', new RuleReferenceNode('bar')));
        $this->createRule($grammar)->parse('foo = bar;', $context)->getRest()->shouldBe('');

        $this->createRule($grammar)->parse('foo = bar', $context)->isSuccess()->shouldBe(false);
        $this->createRule($grammar)->parse('foo:bar;', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_a_grammar_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $definition = 'grammar foo { start foo = the; the = bar; }';

        $tree = new GrammarNode('foo', 'foo', array(
            new RuleNode('foo', new RuleReferenceNode('the')),
            new RuleNode('the', new RuleReferenceNode('bar')),
        ));

        $this->createGrammar($grammar)->parse($definition, $context)->isSuccess()->shouldBe(true);
        $this->createGrammar($grammar)->parse($definition, $context)->getResult()->shouldBeLike($tree);
        $this->createGrammar($grammar)->parse($definition, $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_peg_file_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $definition = ' grammar foo { start foo = the; } ';

        $tree = new GrammarNode('foo', 'foo', array(
            new RuleNode('foo', new RuleReferenceNode('the')),
        ));

        $this->createPegFile($grammar)->parse($definition, $context)->isSuccess()->shouldBe(true);
        $this->createPegFile($grammar)->parse($definition, $context)->getResult()->shouldBeLike($tree);
        $this->createPegFile($grammar)->parse($definition, $context)->getRest()->shouldBe('');
    }
}
