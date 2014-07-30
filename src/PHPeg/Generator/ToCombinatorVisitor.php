<?php

namespace PHPeg\Generator;

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
use PHPeg\Grammar\Tree\VisitorInterface;
use PHPeg\Grammar\Tree\ZeroOrMoreNode;

class ToCombinatorVisitor implements VisitorInterface
{
    private $grammar;
    private $results = array();

    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    public function getResult()
    {
        return array_pop($this->results);
    }

    private function currentResult()
    {
        if (empty($this->results)) {
            return null;
        }

        return $this->results[count($this->results) - 1];
    }

    private function getResults($count)
    {
        $results = array();

        for ($i = 0; $i < $count; $i++) {
            array_unshift($results, array_pop($this->results));
        }

        return $results;
    }

    public function visitAction(ActionNode $node)
    {
        $this->results[] = new Action($this->getResult(), $node->getCode());
    }

    public function visitAndPredicate(AndPredicateNode $node)
    {
        $this->results[] = new AndPredicate($this->getResult());
    }

    public function visitAny(AnyNode $node)
    {
        $this->results[] = new Any();
    }

    public function visitCharacterClass(CharacterClassNode $node)
    {
        $this->results[] = new CharacterClass($node->getString());
    }

    public function visitChoice(ChoiceNode $node)
    {
        $this->results[] = new Choice($this->getResults($node->getLength()));
    }

    public function visitGrammar(GrammarNode $node)
    {
        $this->grammar->setStartSymbol($node->getStartSymbol());
    }

    public function visitLabel(LabelNode $node)
    {
        $this->results[] = new Label($node->getName(), $this->getResult());
    }

    public function visitLiteral(LiteralNode $node)
    {
        $this->results[] = new Literal($node->getString());
    }

    public function visitMatchedString(MatchedStringNode $node)
    {
        $this->results[] = new MatchedString($this->getResult());
    }

    public function visitNotPredicate(NotPredicateNode $node)
    {
        $this->results[] = new NotPredicate($this->getResult());
    }

    public function visitOneOrMore(OneOrMoreNode $node)
    {
        $this->results[] = new OneOrMore($this->getResult());
    }

    public function visitOptional(OptionalNode $node)
    {
        $this->results[] = new Optional($this->getResult());
    }

    public function visitRule(RuleNode $node)
    {
        $this->grammar->addRule($node->getName(), $this->getResult());
    }

    public function visitRuleReference(RuleReferenceNode $node)
    {
        $this->results[] = new RuleReference($this->grammar, $node->getName());
    }

    public function visitSequence(SequenceNode $node)
    {
        $this->results[] = new Sequence($this->getResults($node->getLength()));
    }

    public function visitZeroOrMore(ZeroOrMoreNode $node)
    {
        $this->results[] = new ZeroOrMore($this->getResult());
    }
}
