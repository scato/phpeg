<?php

namespace spec\PHPeg\Generator;

use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\AndActionNode;
use PHPeg\Grammar\Tree\AndPredicateNode;
use PHPeg\Grammar\Tree\AnyNode;
use PHPeg\Grammar\Tree\CharacterClassNode;
use PHPeg\Grammar\Tree\ChoiceNode;
use PHPeg\Grammar\Tree\CutNode;
use PHPeg\Grammar\Tree\GrammarNode;
use PHPeg\Grammar\Tree\LabelNode;
use PHPeg\Grammar\Tree\LiteralNode;
use PHPeg\Grammar\Tree\MatchedStringNode;
use PHPeg\Grammar\Tree\NotActionNode;
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

    function it_should_create_an_and_action_from_a_node()
    {
        $andActionNode = new SequenceNode(array(new LabelNode('name', new AnyNode()), new AndActionNode('return true;')));
        $andActionCode = <<<EOS
\$_value2 = array();

if (\$this->position < strlen(\$this->string)) {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, 1);
    \$this->position += 1;
} else {
    \$_success = false;
}

if (\$_success) {
    \$name = \$this->value;
}

if (\$_success) {
    \$_value2[] = \$this->value;

    \$_position1 = \$this->position;

    \$_success = call_user_func(function () use (&\$name) {
        return true;
    });

    if (\$_success) {
        \$this->value = null;
    }

    \$this->position = \$_position1;
}

if (\$_success) {
    \$_value2[] = \$this->value;

    \$this->value = \$_value2;
}
EOS;
        $andActionNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($andActionCode);
    }

    function it_should_create_an_and_predicate_from_a_node()
    {
        $andPredicateNode = new AndPredicateNode(new RuleReferenceNode('Foo'));
        $andPredicateCode = <<<EOS
\$_position1 = \$this->position;

\$_success = \$this->parseFoo();

if (\$_success) {
    \$this->value = null;
}

\$this->position = \$_position1;
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
\$_position1 = \$this->position;
\$_cut2 = \$this->cut;

\$this->cut = false;
\$_success = \$this->parseFoo();

if (!\$_success && !\$this->cut) {
    \$this->position = \$_position1;

    \$_success = \$this->parseBar();
}

\$this->cut = \$_cut2;
EOS;

        $choiceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($choiceCode);
    }

    function it_should_create_a_cut_from_a_node()
    {
        $cutNode = new CutNode();
        $cutCode = <<<EOS
\$_success = true;
\$this->value = null;

\$this->cut = true;
EOS;

        $cutNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($cutCode);
    }

    function it_should_create_a_grammar_from_a_node()
    {
        $grammarNode = new GrammarNode('FooFile', array(new RuleNode('Foo', new RuleReferenceNode('Bar'))));
        $grammarNode->setNamespace('Acme\\Factory');
        $grammarNode->setImports(array('Acme\\FactoryInterface'));
        $grammarNode->setStartSymbol('Foo');
        $grammarCode = <<<EOS
namespace Acme\Factory;

use Acme\FactoryInterface;

class FooFile
{
    protected \$string;
    protected \$position;
    protected \$value;
    protected \$cut = false;
    protected \$cache;
    protected \$errors = array();
    protected \$warnings = array();

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

        if (!\$_success) {
            \$this->report(\$_position, 'Foo');
        }

        return \$_success;
    }

    private function line()
    {
        return count(explode("\\n", substr(\$this->string, 0, \$this->position)));
    }

    private function rest()
    {
        return '"' . substr(\$this->string, \$this->position) . '"';
    }

    protected function report(\$position, \$expecting)
    {
        if (\$this->cut && !isset(\$this->errors[\$position])) {
            \$this->errors[\$position] = \$expecting;
        }

        if (!\$this->cut) {
            \$this->warnings[\$position][] = \$expecting;
        }
    }

    private function expecting()
    {
        if (!empty(\$this->errors)) {
            ksort(\$this->errors);

            return end(\$this->errors);
        }

        ksort(\$this->warnings);

        return implode(', ', end(\$this->warnings));
    }

    public function parse(\$_string)
    {
        \$this->cache = array();
        \$this->string = \$_string;
        \$this->position = 0;

        \$_success = \$this->parseFoo();

        if (!\$_success) {
            throw new \InvalidArgumentException("Syntax error, expecting {\$this->expecting()} on line {\$this->line()}");
        }

        if (\$this->position < strlen(\$this->string)) {
            throw new \InvalidArgumentException("Syntax error, unexpected {\$this->rest()} on line {\$this->line()}");
        }

        return \$this->value;
    }
}
EOS;

        $grammarNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($grammarCode);
    }

    function it_should_create_an_extended_grammar_from_a_node()
    {
        $grammarNode = new GrammarNode('FooFile', array(new RuleNode('Foo', new RuleReferenceNode('Bar'))));
        $grammarNode->setNamespace('Acme\\Factory');
        $grammarNode->setImports(array('Acme\\FactoryInterface', 'Acme\\BaseFile'));
        $grammarNode->setBase('BaseFile');
        $grammarCode = <<<EOS
namespace Acme\Factory;

use Acme\FactoryInterface;
use Acme\BaseFile;

class FooFile extends BaseFile
{
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

        if (!\$_success) {
            \$this->report(\$_position, 'Foo');
        }

        return \$_success;
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
        $literalNode = new LiteralNode('"foo"', false);
        $literalCode = <<<EOS
if (substr(\$this->string, \$this->position, strlen("foo")) === "foo") {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, strlen("foo"));
    \$this->position += strlen("foo");
} else {
    \$_success = false;

    \$this->report(\$this->position, '"foo"');
}
EOS;

        $literalNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($literalCode);

        $literalNode = new LiteralNode('"foo"', true);
        $literalCode = <<<EOS
if (strtolower(substr(\$this->string, \$this->position, strlen("foo"))) === strtolower("foo")) {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, strlen("foo"));
    \$this->position += strlen("foo");
} else {
    \$_success = false;

    \$this->report(\$this->position, '"foo"');
}
EOS;

        $literalNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($literalCode);

        $literalNode = new LiteralNode('"\\n"', false);
        $literalCode = <<<EOS
if (substr(\$this->string, \$this->position, strlen("\\n")) === "\\n") {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, strlen("\\n"));
    \$this->position += strlen("\\n");
} else {
    \$_success = false;

    \$this->report(\$this->position, '"\\\\n"');
}
EOS;

        $literalNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($literalCode);
    }

    function it_should_create_a_matched_string_from_a_node()
    {
        $matchedStringNode = new MatchedStringNode(new RuleReferenceNode('Foo'));
        $matchedStringCode = <<<EOS
\$_position1 = \$this->position;

\$_success = \$this->parseFoo();

if (\$_success) {
    \$this->value = strval(substr(\$this->string, \$_position1, \$this->position - \$_position1));
}
EOS;

        $matchedStringNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($matchedStringCode);
    }

    function it_should_create_a_not_action_from_a_node()
    {
        $notActionNode = new SequenceNode(array(new LabelNode('name', new AnyNode()), new NotActionNode('return false;')));
        $notActionCode = <<<EOS
\$_value2 = array();

if (\$this->position < strlen(\$this->string)) {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, 1);
    \$this->position += 1;
} else {
    \$_success = false;
}

if (\$_success) {
    \$name = \$this->value;
}

if (\$_success) {
    \$_value2[] = \$this->value;

    \$_position1 = \$this->position;

    \$_success = call_user_func(function () use (&\$name) {
        return false;
    });

    if (!\$_success) {
        \$_success = true;
        \$this->value = null;
    } else {
        \$_success = false;
    }

    \$this->position = \$_position1;
}

if (\$_success) {
    \$_value2[] = \$this->value;

    \$this->value = \$_value2;
}
EOS;
        $notActionNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($notActionCode);
    }

    function it_should_create_a_not_predicate_from_a_node()
    {
        $notPredicateNode = new NotPredicateNode(new RuleReferenceNode('Foo'));
        $notPredicateCode = <<<EOS
\$_position1 = \$this->position;

\$_success = \$this->parseFoo();

if (!\$_success) {
    \$_success = true;
    \$this->value = null;
} else {
    \$_success = false;
}

\$this->position = \$_position1;
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
    \$_value2 = array(\$this->value);
    \$_cut3 = \$this->cut;

    while (true) {
        \$_position1 = \$this->position;

        \$this->cut = false;
        \$_success = \$this->parseFoo();

        if (!\$_success) {
            break;
        }

        \$_value2[] = \$this->value;
    }

    if (!\$this->cut) {
        \$_success = true;
        \$this->position = \$_position1;
        \$this->value = \$_value2;
    }

    \$this->cut = \$_cut3;
}
EOS;

        $oneOrMoreNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($oneOrMoreCode);
    }

    function it_should_create_an_optional_from_a_node()
    {
        $optionalNode = new OptionalNode(new RuleReferenceNode('Foo'));
        $optionalCode = <<<EOS
\$_position1 = \$this->position;
\$_cut2 = \$this->cut;

\$this->cut = false;
\$_success = \$this->parseFoo();

if (!\$_success && !\$this->cut) {
    \$_success = true;
    \$this->position = \$_position1;
    \$this->value = null;
}

\$this->cut = \$_cut2;
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

    if (!\$_success) {
        \$this->report(\$_position, 'Foo');
    }

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
\$_value1 = array();

\$_success = \$this->parseFoo();

if (\$_success) {
    \$_value1[] = \$this->value;

    \$_success = \$this->parseBar();
}

if (\$_success) {
    \$_value1[] = \$this->value;

    \$this->value = \$_value1;
}
EOS;

        $sequenceNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($sequenceCode);
    }

    function it_should_create_a_zero_or_more_from_a_node()
    {
        $zeroOrMoreNode = new ZeroOrMoreNode(new RuleReferenceNode('Foo'));
        $zeroOrMoreCode = <<<EOS
\$_value2 = array();
\$_cut3 = \$this->cut;

while (true) {
    \$_position1 = \$this->position;

    \$this->cut = false;
    \$_success = \$this->parseFoo();

    if (!\$_success) {
        break;
    }

    \$_value2[] = \$this->value;
}

if (!\$this->cut) {
    \$_success = true;
    \$this->position = \$_position1;
    \$this->value = \$_value2;
}

\$this->cut = \$_cut3;
EOS;

        $zeroOrMoreNode->accept($this->getWrappedObject());
        $this->getResult()->shouldBe($zeroOrMoreCode);
    }
}
