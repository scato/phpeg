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
            return preg_match('/^File/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('File', strval(substr($args[0], 4)));
        });

        $identifier->parse(Argument::that(function ($string) {
            return preg_match('/^Foo/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('Foo', strval(substr($args[0], 3)));
        });

        $identifier->parse(Argument::that(function ($string) {
            return preg_match('/^The/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('The', strval(substr($args[0], 3)));
        });

        $identifier->parse(Argument::that(function ($string) {
            return preg_match('/^Bar/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('Bar', strval(substr($args[0], 3)));
        });

        $identifier->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function () {
            return new Failure();
        });

        $expression->parse(Argument::that(function ($string) {
            return preg_match('/^The/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(new RuleReferenceNode('The'), strval(substr($args[0], 3)));
        });

        $expression->parse(Argument::that(function ($string) {
            return preg_match('/^Bar/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(new RuleReferenceNode('Bar'), strval(substr($args[0], 3)));
        });

        $expression->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function () {
            return new Failure();
        });

        $grammar->getRule('_')->willReturn($whitespace);
        $grammar->getRule('Identifier')->willReturn($identifier);
        $grammar->getRule('Expression')->willReturn($expression);

        $grammar->getRule('Rule')->willReturn($this->createRule($grammar));
        $grammar->getRule('Grammar')->willReturn($this->createGrammar($grammar));
        $grammar->getRule('Namespace')->willReturn($this->createNamespace($grammar));
    }

    function it_should_create_a_rule_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createRule($grammar)->parse('Foo = Bar;', $context)->isSuccess()->shouldBe(true);
        $this->createRule($grammar)->parse('Foo = Bar;', $context)->getResult()->shouldBeLike(new RuleNode('Foo', new RuleReferenceNode('Bar')));
        $this->createRule($grammar)->parse('Foo = Bar;', $context)->getRest()->shouldBe('');

        $this->createRule($grammar)->parse('Foo = Bar', $context)->isSuccess()->shouldBe(false);
        $this->createRule($grammar)->parse('Foo:Bar;', $context)->isSuccess()->shouldBe(false);
    }

    function it_should_create_a_grammar_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $definition = 'grammar Foo { start File = The; The = Bar; }';

        $tree = new GrammarNode('Foo', 'File', array(
            new RuleNode('File', new RuleReferenceNode('The')),
            new RuleNode('The', new RuleReferenceNode('Bar')),
        ));

        $this->createGrammar($grammar)->parse($definition, $context)->isSuccess()->shouldBe(true);
        $this->createGrammar($grammar)->parse($definition, $context)->getResult()->shouldBeLike($tree);
        $this->createGrammar($grammar)->parse($definition, $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_namespace_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createNamespace($grammar)->parse('namespace The\\Bar;', $context)->isSuccess()->shouldBe(true);
        $this->createNamespace($grammar)->parse('namespace The\\Bar;', $context)->getResult()->shouldBe('The\\Bar');
        $this->createNamespace($grammar)->parse('namespace The\\Bar;', $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_peg_file_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $definition = ' namespace The\\Bar; grammar Foo { start File = Bar; } ';

        $tree = new GrammarNode('Foo', 'File', array(
            new RuleNode('File', new RuleReferenceNode('Bar')),
        ));

        $tree->setNamespace('The\\Bar');

        $this->createPegFile($grammar)->parse($definition, $context)->isSuccess()->shouldBe(true);
        $this->createPegFile($grammar)->parse($definition, $context)->getResult()->shouldBeLike($tree);
        $this->createPegFile($grammar)->parse($definition, $context)->getRest()->shouldBe('');
    }
}
