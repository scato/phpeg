<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\Choice;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\GrammarInterface;

class UnaryRuleFactory
{
    public function createZeroOrMore(GrammarInterface $grammar)
    {
        // ZeroOrMore = expression:Terminal _ "*" { return new ZeroOrMoreNode($expression); };
        return new Action(
            new Sequence(array(
                new Label('expression', new RuleReference($grammar, 'Terminal')),
                new RuleReference($grammar, '_'),
                new Literal('*')
            )),
            'return new \PHPeg\Grammar\Tree\ZeroOrMoreNode($expression);'
        );
    }

    public function createOneOrMore(GrammarInterface $grammar)
    {
        // OneOrMore = expression:Terminal _ "+" { return new OneOrMoreNode($expression); };
        return new Action(
            new Sequence(array(
                new Label('expression', new RuleReference($grammar, 'Terminal')),
                new RuleReference($grammar, '_'),
                new Literal('+')
            )),
            'return new \PHPeg\Grammar\Tree\OneOrMoreNode($expression);'
        );
    }

    public function createOptional(GrammarInterface $grammar)
    {
        // Optional = expression:Terminal _ "?" { return new OptionalNode($expression); };
        return new Action(
            new Sequence(array(
                new Label('expression', new RuleReference($grammar, 'Terminal')),
                new RuleReference($grammar, '_'),
                new Literal('?')
            )),
            'return new \PHPeg\Grammar\Tree\OptionalNode($expression);'
        );
    }

    public function createRepetition(GrammarInterface $grammar)
    {
        // Repetition = ZeroOrMore / OneOrMore / Optional / Terminal;
        return new Choice(array(
            new RuleReference($grammar, 'ZeroOrMore'),
            new RuleReference($grammar, 'OneOrMore'),
            new RuleReference($grammar, 'Optional'),
            new RuleReference($grammar, 'Terminal')
        ));
    }

    public function createAndPredicate(GrammarInterface $grammar)
    {
        // AndPredicate = "&" _ expression:Repetition { return new AndPredicateNode($expression); };
        return new Action(
            new Sequence(array(
                new Literal('&'),
                new RuleReference($grammar, '_'),
                new Label('expression', new RuleReference($grammar, 'Repetition'))
            )),
            'return new \PHPeg\Grammar\Tree\AndPredicateNode($expression);'
        );
    }

    public function createNotPredicate(GrammarInterface $grammar)
    {
        // NotPredicate = "!" _ expression:Repetition { return new NotPredicateNode($expression); };
        return new Action(
            new Sequence(array(
                new Literal('!'),
                new RuleReference($grammar, '_'),
                new Label('expression', new RuleReference($grammar, 'Repetition'))
            )),
            'return new \PHPeg\Grammar\Tree\NotPredicateNode($expression);'
        );
    }

    public function createMatchedString(GrammarInterface $grammar)
    {
        // MatchedString = "$" _ expression:Repetition { return new MatchedStringNode($expression); };
        return new Action(
            new Sequence(array(
                new Literal('$'),
                new RuleReference($grammar, '_'),
                new Label('expression', new RuleReference($grammar, 'Repetition'))
            )),
            'return new \PHPeg\Grammar\Tree\MatchedStringNode($expression);'
        );
    }

    public function createPredicate(GrammarInterface $grammar)
    {
        // Predicate = AndPredicate / NotPredicate / MatchedString / Repetition;
        return new Choice(array(
            new RuleReference($grammar, 'AndPredicate'),
            new RuleReference($grammar, 'NotPredicate'),
            new RuleReference($grammar, 'MatchedString'),
            new RuleReference($grammar, 'Repetition')
        ));
    }
}
