<?php

namespace PHPeg\Grammar;

use PHPeg\Combinator\Grammar;

class ParserFactory
{
    private $terminalRuleFactory;
    private $unaryRuleFactory;
    private $binaryRuleFactory;
    private $grammarRuleFactory;

    public function __construct(
        TerminalRuleFactory $terminalRuleFactory,
        UnaryRuleFactory $unaryRuleFactory,
        BinaryRuleFactory $binaryRuleFactory,
        GrammarRuleFactory $grammarRuleFactory
    ) {
        $this->terminalRuleFactory = $terminalRuleFactory;
        $this->unaryRuleFactory = $unaryRuleFactory;
        $this->binaryRuleFactory = $binaryRuleFactory;
        $this->grammarRuleFactory = $grammarRuleFactory;
    }

    public function createParser()
    {
        $grammar = new Grammar('Grammar');

        $grammar->addRule('_',              $this->terminalRuleFactory->createWhitespace());
        $grammar->addRule('Literal',        $this->terminalRuleFactory->createLiteral());
        $grammar->addRule('Any',            $this->terminalRuleFactory->createAny());
        $grammar->addRule('CharacterClass', $this->terminalRuleFactory->createCharacterClass());
        $grammar->addRule('Identifier',     $this->terminalRuleFactory->createIdentifier());
        $grammar->addRule('RuleReference',  $this->terminalRuleFactory->createRuleReference($grammar));
        $grammar->addRule('SubExpression',  $this->terminalRuleFactory->createSubExpression($grammar));
        $grammar->addRule('Terminal',       $this->terminalRuleFactory->createTerminal($grammar));
        $grammar->addRule('ZeroOrMore',     $this->unaryRuleFactory->createZeroOrMore($grammar));
        $grammar->addRule('OneOrMore',      $this->unaryRuleFactory->createOneOrMore($grammar));
        $grammar->addRule('Optional',       $this->unaryRuleFactory->createOptional($grammar));
        $grammar->addRule('Repetition',     $this->unaryRuleFactory->createRepetition($grammar));
        $grammar->addRule('AndPredicate',   $this->unaryRuleFactory->createAndPredicate($grammar));
        $grammar->addRule('NotPredicate',   $this->unaryRuleFactory->createNotPredicate($grammar));
        $grammar->addRule('Predicate',      $this->unaryRuleFactory->createPredicate($grammar));
        $grammar->addRule('Label',          $this->binaryRuleFactory->createLabel($grammar));
        $grammar->addRule('Sequence',       $this->binaryRuleFactory->createSequence($grammar));
        $grammar->addRule('Code',           $this->binaryRuleFactory->createCode($grammar));
        $grammar->addRule('Action',         $this->binaryRuleFactory->createAction($grammar));
        $grammar->addRule('Choice',         $this->binaryRuleFactory->createChoice($grammar));
        $grammar->addRule('Expression',     $this->binaryRuleFactory->createExpression($grammar));
        $grammar->addRule('Rule',           $this->grammarRuleFactory->createRule($grammar));
        $grammar->addRule('Grammar',        $this->grammarRuleFactory->createGrammar($grammar));

        return new Parser($grammar);
    }
}
