<?php

namespace PHPeg\Grammar\Tree;

interface VisitorInterface
{
    public function visitAction(ActionNode $node);
    public function visitAndPredicate(AndPredicateNode $node);
    public function visitAny(AnyNode $node);
    public function visitCharacterClass(CharacterClassNode $node);
    public function visitChoice(ChoiceNode $node);
    public function visitCut(CutNode $node);
    public function visitGrammar(GrammarNode $node);
    public function visitLabel(LabelNode $node);
    public function visitLiteral(LiteralNode $node);
    public function visitMatchedString(MatchedStringNode $node);
    public function visitNotPredicate(NotPredicateNode $node);
    public function visitOneOrMore(OneOrMoreNode $node);
    public function visitOptional(OptionalNode $node);
    public function visitRule(RuleNode $node);
    public function visitRuleReference(RuleReferenceNode $node);
    public function visitSequence(SequenceNode $node);
    public function visitZeroOrMore(ZeroOrMoreNode $node);
}
