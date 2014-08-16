<?php

namespace spec\PHPeg\Grammar;

use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\AndPredicateNode;
use PHPeg\Grammar\Tree\AnyNode;
use PHPeg\Grammar\Tree\CharacterClassNode;
use PHPeg\Grammar\Tree\ChoiceNode;
use PHPeg\Grammar\Tree\CutNode;
use PHPeg\Grammar\Tree\GrammarNode;
use PHPeg\Grammar\Tree\LabelNode;
use PHPeg\Grammar\Tree\LiteralNode;
use PHPeg\Grammar\Tree\MatchedStringNode;
use PHPeg\Grammar\Tree\NodeInterface;
use PHPeg\Grammar\Tree\NotPredicateNode;
use PHPeg\Grammar\Tree\OneOrMoreNode;
use PHPeg\Grammar\Tree\OptionalNode;
use PHPeg\Grammar\Tree\RuleNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\Tree\SequenceNode;
use PHPeg\Grammar\Tree\ZeroOrMoreNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PegFileSpec extends ObjectBehavior
{
    function a_grammar_containing($string)
    {
        return 'grammar TestFile { start File = ' . $string . '; }';
    }

    function a_tree_containing(NodeInterface $node)
    {
        $grammar = new GrammarNode('TestFile', array(
            new RuleNode('File', $node)
        ));

        $grammar->setStartSymbol('File');

        return $grammar;
    }

    function it_should_parse_literals()
    {
        $this->parse($this->a_grammar_containing('"\\"foo\\""'))->shouldBeLike(
            $this->a_tree_containing(new LiteralNode('"foo"'))
        );
    }

    function it_should_parse_wildcards()
    {
        $this->parse($this->a_grammar_containing('.'))->shouldBeLike(
            $this->a_tree_containing(new AnyNode())
        );
    }

    function it_should_parse_cuts()
    {
        $this->parse($this->a_grammar_containing('^'))->shouldBeLike(
            $this->a_tree_containing(new CutNode())
        );
    }

    function it_should_parse_character_classes()
    {
        $this->parse($this->a_grammar_containing('[a-z\\[\\]]'))->shouldBeLike(
            $this->a_tree_containing(new CharacterClassNode('a-z\\[\\]'))
        );
    }

    function it_should_parse_rule_references()
    {
        $this->parse($this->a_grammar_containing('Foo'))->shouldBeLike(
            $this->a_tree_containing(new RuleReferenceNode('Foo'))
        );

        // "start" is a reserved word
        $this->shouldThrow(new \InvalidArgumentException('Syntax error, expecting Action on line 1'))
            ->duringParse($this->a_grammar_containing('start'));

        // "starter" is okay, though
        $this->parse($this->a_grammar_containing('starter'))->shouldBeLike(
            $this->a_tree_containing(new RuleReferenceNode('starter'))
        );
    }

    function it_should_parse_subexpressions()
    {
        $this->parse($this->a_grammar_containing('( Foo )'))->shouldBeLike(
            $this->a_tree_containing(new RuleReferenceNode('Foo'))
        );
    }

    function it_should_parse_zero_or_more_expressions()
    {
        $this->parse($this->a_grammar_containing('Foo *'))->shouldBeLike(
            $this->a_tree_containing(new ZeroOrMoreNode(new RuleReferenceNode('Foo')))
        );
    }

    function it_should_parse_one_or_more_expressions()
    {
        $this->parse($this->a_grammar_containing('Foo +'))->shouldBeLike(
            $this->a_tree_containing(new OneOrMoreNode(new RuleReferenceNode('Foo')))
        );
    }

    function it_should_parse_optional_expressions()
    {
        $this->parse($this->a_grammar_containing('Foo ?'))->shouldBeLike(
            $this->a_tree_containing(new OptionalNode(new RuleReferenceNode('Foo')))
        );
    }

    function it_should_parse_and_predicate_expressions()
    {
        $this->parse($this->a_grammar_containing('& Foo'))->shouldBeLike(
            $this->a_tree_containing(new AndPredicateNode(new RuleReferenceNode('Foo')))
        );
    }

    function it_should_parse_not_predicate_expressions()
    {
        $this->parse($this->a_grammar_containing('! Foo'))->shouldBeLike(
            $this->a_tree_containing(new NotPredicateNode(new RuleReferenceNode('Foo')))
        );
    }

    function it_should_parse_matched_string_expressions()
    {
        $this->parse($this->a_grammar_containing('$ Foo'))->shouldBeLike(
            $this->a_tree_containing(new MatchedStringNode(new RuleReferenceNode('Foo')))
        );
    }

    function it_should_parse_label_expressions()
    {
        $this->parse($this->a_grammar_containing('name:Foo'))->shouldBeLike(
            $this->a_tree_containing(new LabelNode('name', new RuleReferenceNode('Foo')))
        );
    }

    function it_should_parse_sequence_expressions()
    {
        $this->parse($this->a_grammar_containing('Foo Bar'))->shouldBeLike(
            $this->a_tree_containing(new SequenceNode(array(new RuleReferenceNode('Foo'), new RuleReferenceNode('Bar'))))
        );
    }

    function it_should_parse_action_expressions()
    {
        $this->parse($this->a_grammar_containing('Foo { if (true) { return "foo"; } else { return "bar"; } }'))->shouldBeLike(
            $this->a_tree_containing(new ActionNode(new RuleReferenceNode('Foo'), 'if (true) { return "foo"; } else { return "bar"; }'))
        );
    }

    function it_should_parse_choice_expressions()
    {
        $this->parse($this->a_grammar_containing('Foo / Bar'))->shouldBeLike(
            $this->a_tree_containing(new ChoiceNode(array(new RuleReferenceNode('Foo'), new RuleReferenceNode('Bar'))))
        );
    }

    function it_should_respect_the_operator_precedence()
    {
        $this->parse($this->a_grammar_containing('m:"-"? _ v:$[a-z]* { return $m . $v; } / Num'))->shouldBeLike(
            $this->a_tree_containing(new ChoiceNode(array(
                new ActionNode(new SequenceNode(array(
                    new LabelNode('m', new OptionalNode(new LiteralNode('-'))),
                    new RuleReferenceNode('_'),
                    new LabelNode('v', new MatchedStringNode(new ZeroOrMoreNode(new CharacterClassNode('a-z'))))
                )), 'return $m . $v;'),
                new RuleReferenceNode('Num')
            )))
        );
    }

    function it_should_parse_namespaces_and_imports()
    {
        $tree = new GrammarNode('TestFile', array(
            new RuleNode('File', new LiteralNode('foo'))
        ));

        $tree->setNamespace('Acme\\Test');
        $tree->setImports(array('Acme\\Factory'));
        $tree->setStartSymbol('File');

        $this->parse('namespace Acme\\Test; use Acme\\Factory; grammar TestFile { start File = "foo"; }')->shouldBeLike($tree);
    }

    function it_should_parse_extended_grammars()
    {
        $tree = new GrammarNode('ExtendedFile', array(
            new RuleNode('Foo', new LiteralNode('foo'))
        ));

        $tree->setBase('BaseFile');

        $this->parse('grammar ExtendedFile extends BaseFile { Foo = "foo"; }')->shouldBeLike($tree);
    }

    function it_should_ignore_comments()
    {
        $this->parse($this->a_grammar_containing("Foo // example\n / Bar"))->shouldBeLike(
            $this->a_tree_containing(new ChoiceNode(array(new RuleReferenceNode('Foo'), new RuleReferenceNode('Bar'))))
        );

        $this->parse($this->a_grammar_containing('Foo /* example */ / Bar'))->shouldBeLike(
            $this->a_tree_containing(new ChoiceNode(array(new RuleReferenceNode('Foo'), new RuleReferenceNode('Bar'))))
        );
    }

    function it_should_report_errors()
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('Syntax error, expecting } on line 1'))
            ->duringParse('grammar TestFile { start = "foo"; }');

        $this
            ->shouldThrow(new \InvalidArgumentException('Syntax error, expecting ; on line 1'))
            ->duringParse('grammar TestFile { start File = "foo" }');

        $this
            ->shouldThrow(new \InvalidArgumentException('Syntax error, expecting Action on line 1'))
            ->duringParse('grammar TestFile { start File = "foo" / ; }');

        $this
            ->shouldThrow(new \InvalidArgumentException('Syntax error, unexpected use on line 1'))
            ->duringParse('grammar TestFile { start File = "foo" / "bar"; } use');
    }
}
