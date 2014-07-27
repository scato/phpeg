<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Action;
use PHPeg\Combinator\CharacterClass;
use PHPeg\Combinator\Choice;
use PHPeg\Combinator\Label;
use PHPeg\Combinator\Literal;
use PHPeg\Combinator\RuleReference;
use PHPeg\Combinator\Sequence;
use PHPeg\Combinator\ZeroOrMore;
use PHPeg\GrammarInterface;

class BinaryRuleFactory
{
    public function createLabel(GrammarInterface $grammar)
    {
        // Label = name:Identifier _ ":" _ expression:Predicate { new LabelNode($name, $expression); } / Predicate;
        return new Choice(
            new Action(
                new Sequence(
                    new Sequence(
                        new Sequence(
                            new Sequence(
                                new Label('name', new RuleReference($grammar, 'Identifier')),
                                new RuleReference($grammar, '_')
                            ),
                            new Literal(':')
                        ),
                        new RuleReference($grammar, '_')
                    ),
                    new Label('expression', new RuleReference($grammar, 'Predicate'))
                ),
                'return new \PHPeg\Grammar\Tree\LabelNode($name, $expression);'
            ),
            new RuleReference($grammar, 'Predicate')
        );
    }

    public function createSequence(GrammarInterface $grammar)
    {
        // Sequence = left:Label (left:(_ right:Label { new SequenceNode($left, $right); }))* { return $left; };
        return new Action(
            new Sequence(
                new Label('left', new RuleReference($grammar, 'Label')),
                new ZeroOrMore(
                    new Label(
                        'left',
                        new Action(
                            new Sequence(
                                new RuleReference($grammar, '_'),
                                new Label('right', new RuleReference($grammar, 'Label'))
                            ),
                            'return new \PHPeg\Grammar\Tree\SequenceNode($left, $right);'
                        )
                    )
                )
            ),
            'return $left;'
        );
    }

    public function createCode(GrammarInterface $grammar)
    {
        // Code = ([^{}] / "{" Code "}")*;
        return new ZeroOrMore(new Choice(
            new CharacterClass('^{}'),
            new Sequence(new Sequence(new Literal('{'), new RuleReference($grammar, 'Code')), new Literal('}'))
        ));
    }

    public function createAction(GrammarInterface $grammar)
    {
        // Action = expression:Sequence _ "{" code:Code "}" { new ActionNode($expression, trim($code)); } / Sequence;
        return new Choice(
            new Action(
                new Sequence(
                    new Sequence(
                        new Sequence(
                            new Sequence(
                                new Label('expression', new RuleReference($grammar, 'Sequence')),
                                new RuleReference($grammar, '_')
                            ),
                            new Literal('{')
                        ),
                        new Label('code', new RuleReference($grammar, 'Code'))
                    ),
                    new Literal('}')
                ),
                'return new \PHPeg\Grammar\Tree\ActionNode($expression, trim($code));'
            ),
            new RuleReference($grammar, 'Sequence')
        );
    }

    public function createChoice(GrammarInterface $grammar)
    {
        // Choice = left:Action (left:(_ "/" _ right:Action { new ChoiceNode($left, $right); }))* { return $left; };
        return new Action(
            new Sequence(
                new Label('left', new RuleReference($grammar, 'Action')),
                new ZeroOrMore(
                    new Label(
                        'left',
                        new Action(
                            new Sequence(
                                new Sequence(
                                    new Sequence(
                                        new RuleReference($grammar, '_'),
                                        new Literal("/")
                                    ),
                                    new RuleReference($grammar, '_')
                                ),
                                new Label('right', new RuleReference($grammar, 'Action'))
                            ),
                            'return new \PHPeg\Grammar\Tree\ChoiceNode($left, $right);'
                        )
                    )
                )
            ),
            'return $left;'
        );
    }

    public function createExpression(GrammarInterface $grammar)
    {
        // Expression = Choice;
        return new RuleReference($grammar, 'Choice');
    }
}
