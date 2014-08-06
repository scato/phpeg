<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\MatchedString;
use PHPeg\Combinator\Optional;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\Combinator\ZeroOrMore;
use PHPeg\GrammarInterface;

class GrammarRuleFactory
{
    public function createRule(GrammarInterface $grammar)
    {
        // Rule = name:Identifier _ "=" _ expression:Expression _ ";" { return new RuleNode($name, $expression); };
        return new Action(
            new Sequence(array(
                new Label('name', new RuleReference($grammar, 'Identifier')),
                new RuleReference($grammar, '_'),
                new Literal('='),
                new RuleReference($grammar, '_'),
                new Label('expression', new RuleReference($grammar, 'Expression')),
                new RuleReference($grammar, '_'),
                new Literal(';')
            )),
            'return new \PHPeg\Grammar\Tree\RuleNode($name, $expression);'
        );
    }

    public function createGrammar(GrammarInterface $grammar)
    {
        // Grammar = "grammar" _ name:Identifier _ "{"
        //     _ "start" _ start:Rule
        //     rules:(_ rule:Rule { return $rule; })*
        //     _ "}" { return new GrammarNode($name, $start->getName(), array_merge(array($start), $rules)); };
        return new Action(
            new Sequence(array(
                new Literal('grammar'),
                new RuleReference($grammar, '_'),
                new Label('name', new RuleReference($grammar, 'Identifier')),
                new RuleReference($grammar, '_'),
                new Literal('{'),
                new RuleReference($grammar, '_'),
                new Literal('start'),
                new RuleReference($grammar, '_'),
                new Label('start', new RuleReference($grammar, 'Rule')),
                new Label('rules', new ZeroOrMore(
                    new Action(new Sequence(array(
                        new RuleReference($grammar, '_'),
                        new Label('rule', new RuleReference($grammar, 'Rule'))
                    )), 'return $rule;')
                )),
                new RuleReference($grammar, '_'),
                new Literal('}')
            )),
            'return new \PHPeg\Grammar\Tree\GrammarNode($name, $start->getName(), array_merge(array($start), $rules));'
        );
    }

    public function createQualifiedIdentifier(GrammarInterface $grammar)
    {
        // QualifiedIdentifier = $(Identifier ("\\" Identifier)*);
        return new MatchedString(new Sequence(array(
            new RuleReference($grammar, 'Identifier'),
            new ZeroOrMore(new Sequence(array(new Literal('\\'), new RuleReference($grammar, 'Identifier'))))
        )));
    }

    public function createNamespace(GrammarInterface $grammar)
    {
        // Namespace = "namespace" _ name:$(Identifier ("\\" Identifier)*) _ ";" { return $name; };
        return new Action(
            new Sequence(array(
                new Literal('namespace'),
                new RuleReference($grammar, '_'),
                new Label('name', new RuleReference($grammar, 'QualifiedIdentifier')),
                new RuleReference($grammar, '_'),
                new Literal(';')
            )),
            'return $name;'
        );
    }

    public function createImport(GrammarInterface $grammar)
    {
        // Import = "use" _ name:$(Identifier ("\\" Identifier)*) _ ";" { return $name; };
        return new Action(
            new Sequence(array(
                new Literal('use'),
                new RuleReference($grammar, '_'),
                new Label('name', new RuleReference($grammar, 'QualifiedIdentifier')),
                new RuleReference($grammar, '_'),
                new Literal(';')
            )),
            'return $name;'
        );
    }

    public function createPegFile(GrammarInterface $grammar)
    {
        // PegFile = (_ namespace:Namespace)? imports:(_ import:Import { return $import; })* _ grammar:Grammar _ {
        //     if (isset($namespace)) $grammar->setNamespace($namespace);
        //     $grammar->setImports($imports);
        //     return $grammar;
        // };
        return new Action(
            new Sequence(array(
                new Optional(new Sequence(array(
                    new RuleReference($grammar, '_'),
                    new Label('namespace', new RuleReference($grammar, 'Namespace'))
                ))),
                new Label('imports', new ZeroOrMore(new Action(
                    new Sequence(array(
                        new RuleReference($grammar, '_'),
                        new Label('import', new RuleReference($grammar, 'Import'))
                    )),
                    'return $import;'
                ))),
                new RuleReference($grammar, '_'),
                new Label('grammar', new RuleReference($grammar, 'Grammar')),
                new RuleReference($grammar, '_')
            )),
            'if (isset($namespace)) $grammar->setNamespace($namespace); $grammar->setImports($imports); return $grammar;'
        );
    }
}
