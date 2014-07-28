<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\Any;
use PHPeg\Combinator\CharacterClass;
use PHPeg\Combinator\Choice;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\MatchedString;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\Combinator\ZeroOrMore;
use PHPeg\GrammarInterface;

class TerminalRuleFactory
{
    public function createWhitespace()
    {
        // Whitespace = [ \n\r\t]* { return null; };
        return new Action(
            new ZeroOrMore(new CharacterClass(' \\n\\r\\t')),
            'return null;'
        );
    }

    public function createLiteral()
    {
        // Literal = "\"" string:$([^\\"] / "\\" .)* "\"" { return new LiteralNode(stripslashes($string)); };
        return new Action(
            new Sequence(array(
                new Literal('"'),
                new Label(
                    'string',
                    new MatchedString(new ZeroOrMore(new Choice(array(new CharacterClass('^\\\\"'), new Sequence(array(new Literal('\\'), new Any()))))))
                ),
                new Literal('"')
            )),
            'return new \PHPeg\Grammar\Tree\LiteralNode(stripslashes($string));'
        );
    }

    public function createAny()
    {
        // Any = "." { return new AnyNode(); };
        return new Action(new Literal('.'), 'return new \PHPeg\Grammar\Tree\AnyNode();');
    }

    public function createCharacterClass()
    {
        // CharacterClass = "[" string:$([^\\\]] / "\\" .)* "]" { return new CharacterClassNode($string); };
        return new Action(
            new Sequence(array(
                new Literal('['),
                new Label(
                    'string',
                    new MatchedString(new ZeroOrMore(new Choice(array(new CharacterClass('^\\\\\\]'), new Sequence(array(new Literal('\\'), new Any()))))))
                ),
                new Literal(']')
            )),
            'return new \PHPeg\Grammar\Tree\CharacterClassNode($string);'
        );
    }

    public function createIdentifier()
    {
        // Identifier = $([A-Za-z_] [A-Za-z0-9_]*);
        return new MatchedString(new Sequence(array(new CharacterClass('A-Za-z_'), new ZeroOrMore(new CharacterClass('A-Za-z0-9_')))));
    }

    public function createRuleReference(GrammarInterface $grammar)
    {
        // RuleReference = name:Identifier { new RuleReferenceNode($name); };
        return new Action(
            new Label('name', new RuleReference($grammar, 'Identifier')
            ),
            'return new \PHPeg\Grammar\Tree\RuleReferenceNode($name);'
        );
    }

    public function createSubExpression(GrammarInterface $grammar)
    {
        // SubExpression = "(" _ expression:Expression _ ")" { return $expression; };
        return new Action(
            new Sequence(array(
                new Literal('('),
                new RuleReference($grammar, '_'),
                new Label('expression', new RuleReference($grammar, 'Expression')),
                new RuleReference($grammar, '_'),
                new Literal(')')
            )),
            'return $expression;'
        );
    }

    public function createTerminal(GrammarInterface $grammar)
    {
        // Terminal = Literal / Any / CharacterClass / RuleReference / SubExpression;
        return new Choice(array(
            new RuleReference($grammar, 'Literal'),
            new RuleReference($grammar, 'Any'),
            new RuleReference($grammar, 'CharacterClass'),
            new RuleReference($grammar, 'RuleReference'),
            new RuleReference($grammar, 'SubExpression')
        ));
    }
}
