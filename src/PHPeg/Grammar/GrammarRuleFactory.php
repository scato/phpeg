<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\Combinator\ZeroOrMore;
use PHPeg\GrammarInterface;

class GrammarRuleFactory
{
    public function createRule(GrammarInterface $grammar)
    {
        // Rule = name:Identifier _ "=" _ expression:Expression ";" { return new RuleNode($name, $expression); };
        return new Action(
            new Sequence(
                new Sequence(
                    new Sequence(
                        new Sequence(
                            new Sequence(
                                new Label('name', new RuleReference($grammar, 'Identifier')),
                                new RuleReference($grammar, '_')
                            ),
                            new Literal('=')
                        ),
                        new RuleReference($grammar, '_')
                    ),
                    new Label('expression', new RuleReference($grammar, 'Expression'))
                ),
                new Literal(';')
            ),
            'return new \PHPeg\Grammar\Tree\RuleNode($name, $expression);'
        );
    }

    public function createGrammar(GrammarInterface $grammar)
    {
        // Grammar = "grammar" _ name:Identifier _ "{"
        //     _ "start" _ start:Rule
        //     rules:(_ rule:Rule { return $rule; })*
        //     "}" { return new GrammarNode($name, $start->getName(), array_merge(array($start), $rules)); };
        return new Action(
            new Sequence(
                new Sequence(
                    new Sequence(
                        new Sequence(
                            new Sequence(
                                new Sequence(
                                    new Sequence(
                                        new Sequence(
                                            new Sequence(
                                                new Sequence(
                                                    new Sequence(
                                                        new Literal('grammar'),
                                                        new RuleReference($grammar, '_')
                                                    ),
                                                    new Label('name', new RuleReference($grammar, 'Identifier'))
                                                ),
                                                new RuleReference($grammar, '_')
                                            ),
                                            new Literal('{')
                                        ),
                                        new RuleReference($grammar, '_')
                                    ),
                                    new Literal('start')
                                ),
                                new RuleReference($grammar, '_')
                            ),
                            new Label('start', new RuleReference($grammar, 'Rule'))
                        ),
                        new Label('rules', new ZeroOrMore(
                            new Action(new Sequence(
                                new RuleReference($grammar, '_'),
                                new Label('rule', new RuleReference($grammar, 'Rule'))
                            ), 'return $rule;')
                        ))
                    ),
                    new RuleReference($grammar, '_')
                ),
                new Literal('}')
            ),
            'return new \PHPeg\Grammar\Tree\GrammarNode($name, $start->getName(), array_merge(array($start), array($rules)));'
        );
    }
}
