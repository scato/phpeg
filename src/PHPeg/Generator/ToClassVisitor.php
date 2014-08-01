<?php

namespace PHPeg\Generator;

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

class ToClassVisitor extends AbstractVisitor
{
    private $scope = array();

    private function indent($string)
    {
        return preg_replace('/(?<=\\n)(?!\\n)/', '    ', $string);
    }

    public function visitAction(ActionNode $node)
    {
        if (empty($this->scope)) {
            $use = '';
        } else {
            $use = ' use (' . implode(', ', array_map(function ($name) {
                    return '&$' . $name;
                }, $this->scope)) . ')';
        }

        $this->results[] = <<<EOS
{$this->getResult()}

if (\$_result['success']) {
    \$_result['value'] = call_user_func(function (){$use} {
        {$node->getCode()}
    });
}
EOS;
    }

    public function visitAndPredicate(AndPredicateNode $node)
    {
        $this->results[] = <<<EOS
{$this->getResult()}

if (\$_result['success']) {
    \$_result['value'] = null;
    \$_result['rest'] = \$_string;
}
EOS;
    }

    public function visitAny(AnyNode $node)
    {
        $this->results[] = <<<EOS
if (\$_string !== '') {
    \$_result = array('success' => true, 'value' => substr(\$_string, 0, 1), 'rest' => strval(substr(\$_string, 1)));
} else {
    \$_result = array('success' => false);
}
EOS;
    }

    public function visitCharacterClass(CharacterClassNode $node)
    {
        $pattern = var_export("/^[{$node->getString()}]/", true);

        $this->results[] = <<<EOS
if (preg_match({$pattern}, \$_string)) {
    \$_result = array('success' => true, 'value' => substr(\$_string, 0, 1), 'rest' => strval(substr(\$_string, 1)));
} else {
    \$_result = array('success' => false);
}
EOS;
    }

    public function visitChoice(ChoiceNode $node)
    {
        $pieces = $this->getResults($node->getLength());

        $result = <<<EOS
\$this->strings[] = \$_string;

{$pieces[0]}
EOS;

        foreach (array_slice($pieces, 1) as $piece) {
            $result .= <<<EOS


if (!\$_result['success']) {
    \$_string = end(\$this->strings);
    {$this->indent($piece)}
}
EOS;
        }

        $result .= <<<EOS


array_pop(\$this->strings);
EOS;

        $this->results[] = $result;
    }

    public function visitGrammar(GrammarNode $node)
    {
        $result = <<<EOS
class {$node->getName()} implements \PHPeg\ParserInterface
{
    protected \$strings = array();
    protected \$values = array();

EOS;

        $pieces = $this->getResults($node->getLength());

        foreach ($pieces as $piece) {
            $result .= <<<EOS

    {$this->indent($piece)}
EOS;
        }

        $result .= <<<EOS


    public function parse(\$_string)
    {
        \$_result = \$this->parse{$node->getStartSymbol()}(\$_string);

        if (!\$_result['success']) {
            throw new \InvalidArgumentException("Could not parse '\$_string'");
        }

        if (\$_result['rest'] !== '') {
            throw new \InvalidArgumentException("Unexpected input: '{\$_result['rest']}'");
        }

        return \$_result['value'];
    }
}
EOS;

        $this->results[] = $result;
    }

    public function visitLabel(LabelNode $node)
    {
        $this->scope[] = $node->getName();

        $this->results[] = <<<EOS
{$this->getResult()}

if (\$_result['success']) {
    \${$node->getName()} = \$_result['value'];
}
EOS;
    }

    public function visitLiteral(LiteralNode $node)
    {
        $strlen = strlen($node->getString());
        $var_export = var_export($node->getString(), true);

        $this->results[] = <<<EOS
if (substr(\$_string, 0, {$strlen}) === {$var_export}) {
    \$_result = array('success' => true, 'value' => substr(\$_string, 0, {$strlen}), 'rest' => strval(substr(\$_string, {$strlen})));
} else {
    \$_result = array('success' => false);
}
EOS;
    }

    public function visitMatchedString(MatchedStringNode $node)
    {
        $this->results[] = <<<EOS
\$this->strings[] = \$_string;
{$this->getResult()}

if (\$_result['success']) {
    \$_result['value'] = strval(substr(end(\$this->strings), 0, strlen(end(\$this->strings)) - strlen(\$_result['rest'])));
}

array_pop(\$this->strings);
EOS;
    }

    public function visitNotPredicate(NotPredicateNode $node)
    {
        $this->results[] = <<<EOS
{$this->getResult()}

if (!\$_result['success']) {
    \$_result['success'] = true;
    \$_result['value'] = null;
    \$_result['rest'] = \$_string;
} else {
    \$_result['success'] = false;
}
EOS;
    }

    public function visitOneOrMore(OneOrMoreNode $node)
    {
        $result = $this->getResult();

        $this->results[] = <<<EOS
{$result}

if (\$_result['success']) {
    \$this->values[] = array(\$_result['value']);

    while (true) {
        {$this->indent($this->indent($result))}

        if (!\$_result['success']) {
            break;
        }

        \$this->values[] = array_merge(array_pop(\$this->values), array(\$_result['value']));
        \$_string = \$_result['rest'];
    }

    \$_result['success'] = true;
    \$_result['value'] = array_pop(\$this->values);
    \$_result['rest'] = \$_string;
}
EOS;
    }

    public function visitOptional(OptionalNode $node)
    {
        $this->results[] = <<<EOS
{$this->getResult()}

if (!\$_result['success']) {
    \$_result['success'] = true;
    \$_result['value'] = null;
    \$_result['rest'] = \$_string;
}
EOS;
    }

    public function visitRule(RuleNode $node)
    {
        $this->scope = array();

        $this->results[] = <<<EOS
protected function parse{$node->getName()}(\$_string)
{
    {$this->indent($this->getResult())}

    return \$_result;
}
EOS;
    }

    public function visitRuleReference(RuleReferenceNode $node)
    {
        $this->results[] = <<<EOS
\$_result = \$this->parse{$node->getName()}(\$_string);
EOS;
    }

    public function visitSequence(SequenceNode $node)
    {
        $pieces = $this->getResults($node->getLength());

        $result = <<<EOS
\$this->values[] = array();

{$pieces[0]}
EOS;

        foreach (array_slice($pieces, 1) as $piece) {
            $result .= <<<EOS


if (\$_result['success']) {
    \$this->values[] = array_merge(array_pop(\$this->values), array(\$_result['value']));
    \$_string = \$_result['rest'];

    {$this->indent($piece)}
}
EOS;
        }

        $result .= <<<EOS


if (\$_result['success']) {
    \$_result['value'] = array_pop(\$this->values);
} else {
    array_pop(\$this->values);
}
EOS;

        $this->results[] = $result;
    }

    public function visitZeroOrMore(ZeroOrMoreNode $node)
    {
        $this->results[] = <<<EOS
\$this->values[] = array();

while (true) {
    {$this->indent($this->getResult())}

    if (!\$_result['success']) {
        break;
    }

    \$this->values[] = array_merge(array_pop(\$this->values), array(\$_result['value']));
    \$_string = \$_result['rest'];
}

\$_result['success'] = true;
\$_result['value'] = array_pop(\$this->values);
\$_result['rest'] = \$_string;
EOS;
    }
}
