<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Combinator\Context;
use PHPeg\Combinator\Failure;
use PHPeg\Combinator\Success;
use PHPeg\ExpressionInterface;
use PHPeg\Grammar\TerminalRuleFactory;
use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\ChoiceNode;
use PHPeg\Grammar\Tree\LabelNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\Tree\SequenceNode;
use PHPeg\GrammarInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BinaryRuleFactorySpec extends ObjectBehavior
{
    function let(GrammarInterface $grammar, ExpressionInterface $whitespace, ExpressionInterface $ruleReference)
    {
        $whitespace->parse(Argument::that(function ($string) {
            return preg_match('/^ /', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success(' ', strval(substr($args[0], 1)));
        });

        $whitespace->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Success('', $args[0]);
        });

        $ruleReference->parse(Argument::that(function ($string) {
            return preg_match('/^foo/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
                return new Success(new RuleReferenceNode('foo'), strval(substr($args[0], 3)));
            });

        $ruleReference->parse(Argument::that(function ($string) {
            return preg_match('/^the/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
                return new Success(new RuleReferenceNode('the'), strval(substr($args[0], 3)));
            });

        $ruleReference->parse(Argument::that(function ($string) {
            return preg_match('/^bar/', $string);
        }), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
                return new Success(new RuleReferenceNode('bar'), strval(substr($args[0], 3)));
            });

        $ruleReference->parse(Argument::any(), Argument::type('\PHPeg\ContextInterface'))->will(function ($args) {
            return new Failure();
        });

        $grammar->getRule('_')->willReturn($whitespace);
        $grammar->getRule('Predicate')->willReturn($ruleReference);

        $grammar->getRule('Label')->willReturn($this->createLabel($grammar));
        $grammar->getRule('Sequence')->willReturn($this->createSequence($grammar));
        $grammar->getRule('Code')->willReturn($this->createCode($grammar));
        $grammar->getRule('Action')->willReturn($this->createAction($grammar));
        $grammar->getRule('Choice')->willReturn($this->createChoice($grammar));
    }

    function it_should_create_a_label_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createLabel($grammar)->parse('name:foo', $context)->isSuccess()->shouldBe(true);
        $this->createLabel($grammar)->parse('name:foo', $context)->getResult()->shouldBeLike(new LabelNode('name', new RuleReferenceNode('foo')));
        $this->createLabel($grammar)->parse('name:foo', $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_sequence_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createSequence($grammar)->parse('foo bar', $context)->isSuccess()->shouldBe(true);
        $this->createSequence($grammar)->parse('foo bar', $context)->getResult()->shouldBeLike(new SequenceNode(new RuleReferenceNode('foo'), new RuleReferenceNode('bar')));
        $this->createSequence($grammar)->parse('foo bar', $context)->getRest()->shouldBe('');

        $this->createSequence($grammar)->parse('foo the bar', $context)->isSuccess()->shouldBe(true);
        $this->createSequence($grammar)->parse('foo the bar', $context)->getResult()->shouldBeLike(new SequenceNode(new SequenceNode(new RuleReferenceNode('foo'), new RuleReferenceNode('the')), new RuleReferenceNode('bar')));
        $this->createSequence($grammar)->parse('foo the bar', $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_code_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $code = 'if (true) { return 1; } else { return 2; }';

        $this->createCode($grammar)->parse($code, $context)->isSuccess()->shouldBe(true);
        $this->createCode($grammar)->parse($code, $context)->getResult()->shouldBe($code);
        $this->createCode($grammar)->parse($code, $context)->getRest()->shouldBe('');

        $this->createCode($grammar)->parse($code . '}', $context)->isSuccess()->shouldBe(true);
        $this->createCode($grammar)->parse($code . '}', $context)->getResult()->shouldBe($code);
        $this->createCode($grammar)->parse($code . '}', $context)->getRest()->shouldBe('}');
    }

    function it_should_create_an_action_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createAction($grammar)->parse('foo { return "bar"; }', $context)->isSuccess()->shouldBe(true);
        $this->createAction($grammar)->parse('foo { return "bar"; }', $context)->getResult()->shouldBeLike(new ActionNode(new RuleReferenceNode('foo'), 'return "bar";'));
        $this->createAction($grammar)->parse('foo { return "bar"; }', $context)->getRest()->shouldBe('');
    }

    function it_should_create_a_choice_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createChoice($grammar)->parse('foo / bar', $context)->isSuccess()->shouldBe(true);
        $this->createChoice($grammar)->parse('foo / bar', $context)->getResult()->shouldBeLike(new ChoiceNode(new RuleReferenceNode('foo'), new RuleReferenceNode('bar')));
        $this->createChoice($grammar)->parse('foo / bar', $context)->getRest()->shouldBe('');

        $this->createChoice($grammar)->parse('foo / the / bar', $context)->isSuccess()->shouldBe(true);
        $this->createChoice($grammar)->parse('foo / the / bar', $context)->getResult()->shouldBeLike(new ChoiceNode(new ChoiceNode(new RuleReferenceNode('foo'), new RuleReferenceNode('the')), new RuleReferenceNode('bar')));
        $this->createChoice($grammar)->parse('foo / the / bar', $context)->getRest()->shouldBe('');
    }

    function it_should_create_an_expression_rule(GrammarInterface $grammar)
    {
        $context = new Context();

        $this->createExpression($grammar)->parse('left:foo right:bar / bar { return "bar"; }', $context)->isSuccess()->shouldBe(true);
        $this->createExpression($grammar)->parse('left:foo right:bar / bar { return "bar"; }', $context)->getRest()->shouldBe('');
    }
}
