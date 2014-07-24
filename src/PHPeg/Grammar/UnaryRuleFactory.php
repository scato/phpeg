<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\GrammarInterface;

class UnaryRuleFactory
{
    public function createZeroOrMore(GrammarInterface $grammar)
    {
        // ZeroOrMore = expression:Expression _ "*" { return new ZeroOrMoreNode($expression); };
        return new Action(
            new Sequence(
                new Sequence(
                    new Label('expression', new RuleReference($grammar, 'Expression')),
                    new RuleReference($grammar, '_')
                ),
                new Literal('*')
            ),
            'return new \PHPeg\Grammar\Tree\ZeroOrMoreNode($expression);'
        );
    }

    public function createOneOrMore(GrammarInterface $grammar)
    {
        // OneOrMore = expression:Expression _ "+" { return new OneOrMoreNode($expression); };
        return new Action(
            new Sequence(
                new Sequence(
                    new Label('expression', new RuleReference($grammar, 'Expression')),
                    new RuleReference($grammar, '_')
                ),
                new Literal('+')
            ),
            'return new \PHPeg\Grammar\Tree\OneOrMoreNode($expression);'
        );
    }

    public function createOptional(GrammarInterface $grammar)
    {
        // Optional = expression:Expression _ "?" { return new OptionalNode($expression); };
        return new Action(
            new Sequence(
                new Sequence(
                    new Label('expression', new RuleReference($grammar, 'Expression')),
                    new RuleReference($grammar, '_')
                ),
                new Literal('?')
            ),
            'return new \PHPeg\Grammar\Tree\OptionalNode($expression);'
        );
    }
}
