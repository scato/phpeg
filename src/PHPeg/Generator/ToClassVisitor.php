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

if (\$_success) {
    \$this->value = call_user_func(function (){$use} {
        {$node->getCode()}
    });
}
EOS;
    }

    public function visitAndPredicate(AndPredicateNode $node)
    {
        $this->results[] = <<<EOS
\$this->strings[] = \$this->string;

{$this->getResult()}

if (\$_success) {
    \$this->value = null;
}

\$this->string = array_pop(\$this->strings);
EOS;
    }

    public function visitAny(AnyNode $node)
    {
        $this->results[] = <<<EOS
if (\$this->string !== '') {
    \$_success = true;
    \$this->value = substr(\$this->string, 0, 1);
    \$this->string = strval(substr(\$this->string, 1));
} else {
    \$_success = false;
}
EOS;
    }

    public function visitCharacterClass(CharacterClassNode $node)
    {
        $pattern = var_export("/^[{$node->getString()}]/", true);

        $this->results[] = <<<EOS
if (preg_match({$pattern}, \$this->string)) {
    \$_success = true;
    \$this->value = substr(\$this->string, 0, 1);
    \$this->string = strval(substr(\$this->string, 1));
} else {
    \$_success = false;
}
EOS;
    }

    public function visitChoice(ChoiceNode $node)
    {
        $pieces = $this->getResults($node->getLength());

        $result = <<<EOS
\$this->strings[] = \$this->string;

{$pieces[0]}
EOS;

        foreach (array_slice($pieces, 1) as $piece) {
            $result .= <<<EOS


if (!\$_success) {
    \$this->string = end(\$this->strings);
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
    protected \$string;
    protected \$strings = array();
    protected \$value;
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
        \$this->string = \$_string;
        \$_success = \$this->parse{$node->getStartSymbol()}();

        if (!\$_success) {
            throw new \InvalidArgumentException("Could not parse '{\$this->string}'");
        }

        if (\$this->string !== '') {
            throw new \InvalidArgumentException("Unexpected input: '{\$this->string}'");
        }

        return \$this->value;
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

if (\$_success) {
    \${$node->getName()} = \$this->value;
}
EOS;
    }

    public function visitLiteral(LiteralNode $node)
    {
        $strlen = strlen($node->getString());
        $var_export = var_export($node->getString(), true);

        $this->results[] = <<<EOS
if (substr(\$this->string, 0, {$strlen}) === {$var_export}) {
    \$_success = true;
    \$this->value = substr(\$this->string, 0, {$strlen});
    \$this->string = strval(substr(\$this->string, {$strlen}));
} else {
    \$_success = false;
}
EOS;
    }

    public function visitMatchedString(MatchedStringNode $node)
    {
        $this->results[] = <<<EOS
\$this->strings[] = \$this->string;
{$this->getResult()}

if (\$_success) {
    \$this->value = strval(substr(end(\$this->strings), 0, strlen(end(\$this->strings)) - strlen(\$this->string)));
}

array_pop(\$this->strings);
EOS;
    }

    public function visitNotPredicate(NotPredicateNode $node)
    {
        $this->results[] = <<<EOS
\$this->strings[] = \$this->string;

{$this->getResult()}

if (!\$_success) {
    \$_success = true;
    \$this->value = null;
} else {
    \$_success = false;
}

\$this->string = array_pop(\$this->strings);
EOS;
    }

    public function visitOneOrMore(OneOrMoreNode $node)
    {
        $result = $this->getResult();

        $this->results[] = <<<EOS
{$result}

if (\$_success) {
    \$this->values[] = array(\$this->value);

    while (true) {
        {$this->indent($this->indent($result))}

        if (!\$_success) {
            break;
        }

        \$this->values[] = array_merge(array_pop(\$this->values), array(\$this->value));
    }

    \$_success = true;
    \$this->value = array_pop(\$this->values);
}
EOS;
    }

    public function visitOptional(OptionalNode $node)
    {
        $this->results[] = <<<EOS
{$this->getResult()}

if (!\$_success) {
    \$_success = true;
    \$this->value = null;
}
EOS;
    }

    public function visitRule(RuleNode $node)
    {
        $this->scope = array();

        $this->results[] = <<<EOS
protected function parse{$node->getName()}()
{
    {$this->indent($this->getResult())}

    return \$_success;
}
EOS;
    }

    public function visitRuleReference(RuleReferenceNode $node)
    {
        $this->results[] = <<<EOS
\$_success = \$this->parse{$node->getName()}();
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


if (\$_success) {
    \$this->values[] = array_merge(array_pop(\$this->values), array(\$this->value));

    {$this->indent($piece)}
}
EOS;
        }

        $result .= <<<EOS


if (\$_success) {
    \$this->value = array_pop(\$this->values);
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

    if (!\$_success) {
        break;
    }

    \$this->values[] = array_merge(array_pop(\$this->values), array(\$this->value));
}

\$_success = true;
\$this->value = array_pop(\$this->values);
EOS;
    }
}
