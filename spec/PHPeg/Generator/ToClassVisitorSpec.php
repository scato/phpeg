<?php

namespace spec\PHPeg\Generator;

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
use PHPeg\Grammar\Tree\ZeroOrMoreNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ToClassVisitorSpec extends ObjectBehavior
{
    function it_should_create_an_action_from_a_node()
    {
        $actionNode = new ActionNode(new LabelNode('name', new RuleReferenceNode('Foo')), 'return $name;');
        $actionCode = <<<EOS
\$_success = \$this->parseFoo();

if (\$_success) {
    \$name = \$this->value;
}

if (\$_success) {
    \$this->value = call_user_func(function () use (&\$name) {
        return \$name;
    });
}
EOS;
        $actionNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($actionCode);
    }

    function it_should_create_an_and_predicate_from_a_node()
    {
        $andPredicateNode = new AndPredicateNode(new RuleReferenceNode('Foo'));
        $andPredicateCode = <<<EOS
\$this->positions[] = \$this->position;

\$_success = \$this->parseFoo();

if (\$_success) {
    \$this->value = null;
}

\$this->position = array_pop(\$this->positions);
EOS;

        $andPredicateNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($andPredicateCode);
    }

    function it_should_create_an_any_from_a_node()
    {
        $anyNode = new AnyNode();
        $anyCode = <<<EOS
if (\$this->position < strlen(\$this->string)) {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, 1);
    \$this->position += 1;
} else {
    \$_success = false;
}
EOS;

        $anyNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($anyCode);
    }

    function it_should_create_a_character_class_from_a_node()
    {
        $characterClassNode = new CharacterClassNode('a-z');
        $characterClassCode = <<<EOS
if (preg_match('/^[a-z]$/', substr(\$this->string, \$this->position, 1))) {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, 1);
    \$this->position += 1;
} else {
    \$_success = false;
}
EOS;

        $characterClassNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($characterClassCode);
    }

    function it_should_create_a_choice_from_a_node()
    {
        $choiceNode = new ChoiceNode(array(new RuleReferenceNode('Foo'), new RuleReferenceNode('Bar')));
        $choiceCode = <<<EOS
\$this->positions[] = \$this->position;

\$_success = \$this->parseFoo();

if (!\$_success) {
    \$this->position = end(\$this->positions);
    \$_success = \$this->parseBar();
}

array_pop(\$this->positions);
EOS;

        $choiceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($choiceCode);
    }

    function it_should_create_a_grammar_from_a_node()
    {
        $grammarNode = new GrammarNode('FooFile', 'Foo', array(new RuleNode('Foo', new RuleReferenceNode('Bar'))));
        $grammarNode->setNamespace('Acme\\Factory');
        $grammarCode = <<<EOS
namespace Acme\Factory;

class FooFile implements \PHPeg\ParserInterface
{
    protected \$string;
    protected \$position;
    protected \$positions = array();
    protected \$value;
    protected \$values = array();
    protected \$cache;

    protected function parseFoo()
    {
        \$_position = \$this->position;

        if (isset(\$this->cache['Foo'][\$_position])) {
            \$_success = \$this->cache['Foo'][\$_position]['success'];
            \$this->position = \$this->cache['Foo'][\$_position]['position'];
            \$this->value = \$this->cache['Foo'][\$_position]['value'];

            return \$_success;
        }

        \$_success = \$this->parseBar();

        \$this->cache['Foo'][\$_position] = array(
            'success' => \$_success,
            'position' => \$this->position,
            'value' => \$this->value
        );

        return \$_success;
    }

    public function parse(\$_string)
    {
        \$this->cache = array();
        \$this->string = \$_string;
        \$this->position = 0;

        \$_success = \$this->parseFoo();

        if (!\$_success) {
            throw new \InvalidArgumentException("Could not parse '{\$this->string}'");
        }

        if (\$this->position < strlen(\$this->string)) {
            throw new \InvalidArgumentException("Unexpected input at position: {\$this->position}");
        }

        return \$this->value;
    }
}
EOS;

        $grammarNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($grammarCode);
    }

    function it_should_create_a_label_from_a_node()
    {
        $labelNode = new LabelNode('name', new RuleReferenceNode('Foo'));
        $labelCode = <<<EOS
\$_success = \$this->parseFoo();

if (\$_success) {
    \$name = \$this->value;
}
EOS;

        $labelNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($labelCode);
    }

    function it_should_create_a_literal_from_a_node()
    {
        $literalNode = new LiteralNode('foo');
        $literalCode = <<<EOS
if (substr(\$this->string, \$this->position, 3) === 'foo') {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, 3);
    \$this->position += 3;
} else {
    \$_success = false;
}
EOS;

        $literalNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($literalCode);
    }

    function it_should_create_a_matched_string_from_a_node()
    {
        $matchedStringNode = new MatchedStringNode(new RuleReferenceNode('Foo'));
        $matchedStringCode = <<<EOS
\$this->positions[] = \$this->position;
\$_success = \$this->parseFoo();

if (\$_success) {
    \$this->value = strval(substr(\$this->string, end(\$this->positions), \$this->position - end(\$this->positions)));
}

array_pop(\$this->positions);
EOS;

        $matchedStringNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($matchedStringCode);
    }

    function it_should_create_a_not_predicate_from_a_node()
    {
        $notPredicateNode = new NotPredicateNode(new RuleReferenceNode('Foo'));
        $notPredicateCode = <<<EOS
\$this->positions[] = \$this->position;

\$_success = \$this->parseFoo();

if (!\$_success) {
    \$_success = true;
    \$this->value = null;
} else {
    \$_success = false;
}

\$this->position = array_pop(\$this->positions);
EOS;

        $notPredicateNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($notPredicateCode);
    }

    function it_should_create_a_one_or_more_from_a_node()
    {
        $oneOrMoreNode = new OneOrMoreNode(new RuleReferenceNode('Foo'));
        $oneOrMoreCode = <<<EOS
\$_success = \$this->parseFoo();

if (\$_success) {
    \$this->values[] = array(\$this->value);

    while (true) {
        \$_success = \$this->parseFoo();

        if (!\$_success) {
            break;
        }

        \$this->values[] = array_merge(array_pop(\$this->values), array(\$this->value));
    }

    \$_success = true;
    \$this->value = array_pop(\$this->values);
}
EOS;

        $oneOrMoreNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($oneOrMoreCode);
    }

    function it_should_create_an_optional_from_a_node()
    {
        $optionalNode = new OptionalNode(new RuleReferenceNode('Foo'));
        $optionalCode = <<<EOS
\$_success = \$this->parseFoo();

if (!\$_success) {
    \$_success = true;
    \$this->value = null;
}
EOS;

        $optionalNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($optionalCode);
    }

    function it_should_create_a_rule_from_a_node()
    {
        $ruleNode = new RuleNode('Foo', new RuleReferenceNode('Bar'));
        $ruleCode = <<<EOS
protected function parseFoo()
{
    \$_position = \$this->position;

    if (isset(\$this->cache['Foo'][\$_position])) {
        \$_success = \$this->cache['Foo'][\$_position]['success'];
        \$this->position = \$this->cache['Foo'][\$_position]['position'];
        \$this->value = \$this->cache['Foo'][\$_position]['value'];

        return \$_success;
    }

    \$_success = \$this->parseBar();

    \$this->cache['Foo'][\$_position] = array(
        'success' => \$_success,
        'position' => \$this->position,
        'value' => \$this->value
    );

    return \$_success;
}
EOS;

        $ruleNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($ruleCode);
    }

    function it_should_create_a_rule_reference_from_a_node()
    {
        $ruleReferenceNode = new RuleReferenceNode('Foo');
        $ruleReferenceCode = <<<EOS
\$_success = \$this->parseFoo();
EOS;

        $ruleReferenceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($ruleReferenceCode);
    }

    function it_should_create_a_sequence_from_a_node()
    {
        $sequenceNode = new SequenceNode(array(new RuleReferenceNode('Foo'), new RuleReferenceNode('Bar')));
        $sequenceCode = <<<EOS
\$this->values[] = array();

\$_success = \$this->parseFoo();

if (\$_success) {
    \$this->values[] = array_merge(array_pop(\$this->values), array(\$this->value));

    \$_success = \$this->parseBar();
}

if (\$_success) {
    \$this->value = array_pop(\$this->values);
} else {
    array_pop(\$this->values);
}
EOS;

        $sequenceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($sequenceCode);
    }

    function it_should_create_a_zero_or_more_from_a_node()
    {
        $zeroOrMoreNode = new ZeroOrMoreNode(new RuleReferenceNode('Foo'));
        $zeroOrMoreCode = <<<EOS
\$this->values[] = array();

while (true) {
    \$_success = \$this->parseFoo();

    if (!\$_success) {
        break;
    }

    \$this->values[] = array_merge(array_pop(\$this->values), array(\$this->value));
}

\$_success = true;
\$this->value = array_pop(\$this->values);
EOS;

        $zeroOrMoreNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($zeroOrMoreCode);
    }
}
