<?php

namespace spec\PHPeg\Generator;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\AndPredicate;
use PHPeg\Combinator\Any;
use PHPeg\Combinator\CharacterClass;
use PHPeg\Combinator\Choice;
use PHPeg\Combinator\Grammar;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\MatchedString;
use PHPeg\Combinator\NotPredicate;
use PHPeg\Combinator\OneOrMore;
use PHPeg\Combinator\Optional;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\Combinator\ZeroOrMore;
use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\AndPredicateNode;
use PHPeg\Grammar\Tree\AnyNode;
use PHPeg\Grammar\Tree\CharacterClassNode;
use PHPeg\Grammar\Tree\ChoiceNode;
use PHPeg\Grammar\Tree\GrammarNode;
use PHPeg\Grammar\Tree\LabelNode;
use PHPeg\Grammar\Tree\LiteralNode;
use PHPeg\Grammar\Tree\MatchedStringNode;
use PHPeg\Grammar\Tree\NotPredicateNode;
use PHPeg\Grammar\Tree\OneOrMoreNode;
use PHPeg\Grammar\Tree\OptionalNode;
use PHPeg\Grammar\Tree\RuleNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\Tree\SequenceNode;
use PHPeg\Grammar\Tree\ZeroOrMoreNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ToCombinatorVisitorSpec extends ObjectBehavior
{
    function let(Grammar $grammar)
    {
        $this->beConstructedWith($grammar);
    }

    function it_should_create_an_action_from_a_node(Grammar $grammar)
    {
        $actionNode = new ActionNode(new RuleReferenceNode('foo'), 'return null;');
        $action = new Action(new RuleReference($grammar->getWrappedObject(), 'foo'), 'return null;');

        $actionNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($action);
    }

    function it_should_create_an_and_predicate_from_a_node(Grammar $grammar)
    {
        $andPredicateNode = new AndPredicateNode(new RuleReferenceNode('foo'));
        $andPredicate = new AndPredicate(new RuleReference($grammar->getWrappedObject(), 'foo'));

        $andPredicateNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($andPredicate);
    }

    function it_should_create_an_any_from_a_node()
    {
        $anyNode = new AnyNode();
        $any = new Any();

        $anyNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($any);
    }

    function it_should_create_a_character_class_from_a_node()
    {
        $characterClassNode = new CharacterClassNode('a-z');
        $characterClass = new CharacterClass('a-z');

        $characterClassNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($characterClass);
    }

    function it_should_create_a_choice_from_a_node(Grammar $grammar)
    {
        $choiceNode = new ChoiceNode(array(new RuleReferenceNode('foo'), new RuleReferenceNode('bar')));
        $choice = new Choice(array(new RuleReference($grammar->getWrappedObject(), 'foo'), new RuleReference($grammar->getWrappedObject(), 'bar')));

        $choiceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($choice);
    }

    function it_should_create_a_grammar_from_a_node(Grammar $grammar)
    {
        $grammarNode = new GrammarNode('Foo', 'foo', array(new RuleNode('foo', new RuleReferenceNode('bar'))));

        $grammar->addRule('foo', new RuleReference($grammar->getWrappedObject(), 'bar'))->shouldBeCalled();
        $grammar->setStartSymbol('foo')->shouldBeCalled();

        $grammarNode->accept($this->getWrappedObject());
    }

    function it_should_create_a_label_from_a_node(Grammar $grammar)
    {
        $labelNode = new LabelNode('name', new RuleReferenceNode('foo'));
        $label = new Label('name', new RuleReference($grammar->getWrappedObject(), 'foo'));

        $labelNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($label);
    }

    function it_should_create_a_literal_from_a_node(Grammar $grammar)
    {
        $literalNode = new LiteralNode('foo');
        $literal = new Literal('foo');

        $literalNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($literal);
    }

    function it_should_create_a_matched_string_from_a_node(Grammar $grammar)
    {
        $matchedStringNode = new MatchedStringNode(new RuleReferenceNode('foo'));
        $matchedString = new MatchedString(new RuleReference($grammar->getWrappedObject(), 'foo'));

        $matchedStringNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($matchedString);
    }

    function it_should_create_a_not_predicate_from_a_node(Grammar $grammar)
    {
        $notPredicateNode = new NotPredicateNode(new RuleReferenceNode('foo'));
        $notPredicate = new NotPredicate(new RuleReference($grammar->getWrappedObject(), 'foo'));

        $notPredicateNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($notPredicate);
    }

    function it_should_create_a_one_or_more_from_a_node(Grammar $grammar)
    {
        $oneOrMoreNode = new OneOrMoreNode(new RuleReferenceNode('foo'));
        $oneOrMore = new OneOrMore(new RuleReference($grammar->getWrappedObject(), 'foo'));

        $oneOrMoreNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($oneOrMore);
    }

    function it_should_create_an_optional_from_a_node(Grammar $grammar)
    {
        $optionalNode = new OptionalNode(new RuleReferenceNode('foo'));
        $optional = new Optional(new RuleReference($grammar->getWrappedObject(), 'foo'));

        $optionalNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($optional);
    }

    function it_should_create_a_rule_reference_from_a_node(Grammar $grammar)
    {
        $ruleReferenceNode = new RuleReferenceNode('foo');
        $ruleReference = new RuleReference($grammar->getWrappedObject(), 'foo');

        $ruleReferenceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($ruleReference);
    }

    function it_should_create_a_sequence_from_a_node(Grammar $grammar)
    {
        $sequenceNode = new SequenceNode(array(new RuleReferenceNode('foo'), new RuleReferenceNode('bar')));
        $sequence = new Sequence(array(new RuleReference($grammar->getWrappedObject(), 'foo'), new RuleReference($grammar->getWrappedObject(), 'bar')));

        $sequenceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($sequence);
    }

    function it_should_create_a_zero_or_more_from_a_node(Grammar $grammar)
    {
        $zeroOrMoreNode = new ZeroOrMoreNode(new RuleReferenceNode('foo'));
        $zeroOrMore = new ZeroOrMore(new RuleReference($grammar->getWrappedObject(), 'foo'));

        $zeroOrMoreNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($zeroOrMore);
    }

    function it_should_create_entire_expressions(Grammar $grammar)
    {
        $expressionNode = new ChoiceNode(array(
            new RuleReferenceNode('foo'),
            new SequenceNode(array(new RuleReferenceNode('to'), new RuleReferenceNode('the'))),
            new RuleReferenceNode('bar')
        ));

        $expression = new Choice(array(
            new RuleReference($grammar->getWrappedObject(), 'foo'),
            new Sequence(array(new RuleReference($grammar->getWrappedObject(), 'to'), new RuleReference($grammar->getWrappedObject(), 'the'))),
            new RuleReference($grammar->getWrappedObject(), 'bar')
        ));

        $expressionNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBeLike($expression);
    }
}
