<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\CharacterClass;
use PHPeg\Combinator\Choice;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\MatchedString;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\Combinator\ZeroOrMore;
use PHPeg\GrammarInterface;

class BinaryRuleFactory
{
    public function createLabel(GrammarInterface $grammar)
    {
        // Label = name:Identifier _ ":" _ expression:Predicate { return new LabelNode($name, $expression); } / Predicate;
        return new Choice(array(
            new Action(
                new Sequence(array(
                    new Label('name', new RuleReference($grammar, 'Identifier')),
                    new RuleReference($grammar, '_'),
                    new Literal(':'),
                    new RuleReference($grammar, '_'),
                    new Label('expression', new RuleReference($grammar, 'Predicate'))
                )),
                'return new \PHPeg\Grammar\Tree\LabelNode($name, $expression);'
            ),
            new RuleReference($grammar, 'Predicate')
        ));
    }

    public function createSequence(GrammarInterface $grammar)
    {
        // Sequence = first:Label rest:(_ next:Label { return $next; })* { return empty($rest) ? $first : new SequenceNode(array_merge(array($first), $rest)); };
        return new Action(
            new Sequence(array(
                new Label('first', new RuleReference($grammar, 'Label')),
                new Label(
                    'rest',
                    new ZeroOrMore(
                        new Action(
                            new Sequence(array(
                                new RuleReference($grammar, '_'),
                                new Label('next', new RuleReference($grammar, 'Label'))
                            )),
                            'return $next;'
                        )
                    )
                )
            )),
            'return empty($rest) ? $first : new \PHPeg\Grammar\Tree\SequenceNode(array_merge(array($first), $rest));'
        );
    }

    public function createCode(GrammarInterface $grammar)
    {
        // Code = $([^{}] / "{" Code "}")*;
        return new MatchedString(new ZeroOrMore(new Choice(array(
            new CharacterClass('^{}'),
            new Sequence(array(new Literal('{'), new RuleReference($grammar, 'Code'), new Literal('}')))
        ))));
    }

    public function createAction(GrammarInterface $grammar)
    {
        // Action = expression:Sequence _ "{" code:Code "}" { return new ActionNode($expression, trim($code)); } / Sequence;
        return new Choice(array(
            new Action(
                new Sequence(array(
                    new Label('expression', new RuleReference($grammar, 'Sequence')),
                    new RuleReference($grammar, '_'),
                    new Literal('{'),
                    new Label('code', new RuleReference($grammar, 'Code')),
                    new Literal('}')
                )),
                'return new \PHPeg\Grammar\Tree\ActionNode($expression, trim($code));'
            ),
            new RuleReference($grammar, 'Sequence')
        ));
    }

    public function createChoice(GrammarInterface $grammar)
    {
        // Choice = first:Action rest:(_ "/" _ next:Action { return $next; })* { return empty($rest) ? $first : new ChoiceNode(array_merge(array($first), $rest)); };
        return new Action(
            new Sequence(array(
                new Label('first', new RuleReference($grammar, 'Action')),
                new Label(
                    'rest',
                    new ZeroOrMore(
                        new Action(
                            new Sequence(array(
                                new RuleReference($grammar, '_'),
                                new Literal("/"),
                                new RuleReference($grammar, '_'),
                                new Label('next', new RuleReference($grammar, 'Action'))
                            )),
                            'return $next;'
                        )
                    )
                )
            )),
            'return empty($rest) ? $first : new \PHPeg\Grammar\Tree\ChoiceNode(array_merge(array($first), $rest));'
        );
    }

    public function createExpression(GrammarInterface $grammar)
    {
        // Expression = Choice;
        return new RuleReference($grammar, 'Choice');
    }
}
