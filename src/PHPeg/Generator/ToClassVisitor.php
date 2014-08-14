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
\$this->positions[] = \$this->position;

{$this->getResult()}

if (\$_success) {
    \$this->value = null;
}

\$this->position = array_pop(\$this->positions);
EOS;
    }

    public function visitAny(AnyNode $node)
    {
        $this->results[] = <<<EOS
if (\$this->position < strlen(\$this->string)) {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, 1);
    \$this->position += 1;
} else {
    \$_success = false;
}
EOS;
    }

    public function visitCharacterClass(CharacterClassNode $node)
    {
        $pattern = var_export("/^[{$node->getString()}]$/", true);

        $this->results[] = <<<EOS
if (preg_match({$pattern}, substr(\$this->string, \$this->position, 1))) {
    \$_success = true;
    \$this->value = substr(\$this->string, \$this->position, 1);
    \$this->position += 1;
} else {
    \$_success = false;
}
EOS;
    }

    public function visitChoice(ChoiceNode $node)
    {
        $pieces = $this->getResults($node->getLength());

        $result = <<<EOS
\$this->positions[] = \$this->position;

{$pieces[0]}
EOS;

        foreach (array_slice($pieces, 1) as $piece) {
            $result .= <<<EOS


if (!\$_success) {
    \$this->position = end(\$this->positions);
    {$this->indent($piece)}
}
EOS;
        }

        $result .= <<<EOS


array_pop(\$this->positions);
EOS;

        $this->results[] = $result;
    }

    public function visitGrammar(GrammarNode $node)
    {
        $result = '';

        if ($node->getNamespace() !== null) {
            $result .= "namespace {$node->getNamespace()};\n\n";
        }

        if (count($node->getImports()) > 0) {
            foreach ($node->getImports() as $import) {
                $result .= "use {$import};\n";
            }

            $result .= "\n";
        }

        $result .= <<<EOS
class {$node->getName()}
{
    protected \$string;
    protected \$position;
    protected \$positions = array();
    protected \$value;
    protected \$values = array();
    protected \$cache;
    protected \$expecting = array();

EOS;

        $pieces = $this->getResults($node->getLength());

        foreach ($pieces as $piece) {
            $result .= <<<EOS

    {$this->indent($piece)}

EOS;
        }

        $result .= <<<EOS

    private function line()
    {
        return count(explode("\\n", substr(\$this->string, 0, \$this->position)));
    }

    private function rest()
    {
        return substr(\$this->string, \$this->position);
    }

    private function expecting()
    {
        ksort(\$this->expecting);

        return implode(', ', end(\$this->expecting));
    }

    public function parse(\$_string)
    {
        \$this->cache = array();
        \$this->string = \$_string;
        \$this->position = 0;

        \$_success = \$this->parse{$node->getStartSymbol()}();

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
if (substr(\$this->string, \$this->position, {$strlen}) === {$var_export}) {
    \$_success = true;
    \$this->value = {$var_export};
    \$this->position += {$strlen};
} else {
    \$_success = false;
    \$this->expecting[\$this->position][] = {$var_export};
}
EOS;
    }

    public function visitMatchedString(MatchedStringNode $node)
    {
        $this->results[] = <<<EOS
\$this->positions[] = \$this->position;
{$this->getResult()}

if (\$_success) {
    \$this->value = strval(substr(\$this->string, end(\$this->positions), \$this->position - end(\$this->positions)));
}

array_pop(\$this->positions);
EOS;
    }

    public function visitNotPredicate(NotPredicateNode $node)
    {
        $this->results[] = <<<EOS
\$this->positions[] = \$this->position;

{$this->getResult()}

if (!\$_success) {
    \$_success = true;
    \$this->value = null;
} else {
    \$_success = false;
}

\$this->position = array_pop(\$this->positions);
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
        \$this->positions[] = \$this->position;
        {$this->indent($this->indent($result))}

        if (!\$_success) {
            \$this->position = array_pop(\$this->positions);

            break;
        }

        array_pop(\$this->positions);
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
\$this->positions[] = \$this->position;

{$this->getResult()}

if (!\$_success) {
    \$_success = true;
    \$this->position = end(\$this->positions);
    \$this->value = null;
}

array_pop(\$this->positions);
EOS;
    }

    public function visitRule(RuleNode $node)
    {
        $this->scope = array();

        $this->results[] = <<<EOS
protected function parse{$node->getName()}()
{
    \$_position = \$this->position;

    if (isset(\$this->cache['{$node->getName()}'][\$_position])) {
        \$_success = \$this->cache['{$node->getName()}'][\$_position]['success'];
        \$this->position = \$this->cache['{$node->getName()}'][\$_position]['position'];
        \$this->value = \$this->cache['{$node->getName()}'][\$_position]['value'];

        return \$_success;
    }

    {$this->indent($this->getResult())}

    \$this->cache['{$node->getName()}'][\$_position] = array(
        'success' => \$_success,
        'position' => \$this->position,
        'value' => \$this->value
    );

    if (!\$_success) {
        \$this->expecting[\$_position][] = '{$node->getName()}';
    }

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
    \$this->positions[] = \$this->position;
    {$this->indent($this->getResult())}

    if (!\$_success) {
        \$this->position = array_pop(\$this->positions);

        break;
    }

    array_pop(\$this->positions);
    \$this->values[] = array_merge(array_pop(\$this->values), array(\$this->value));
}

\$_success = true;
\$this->value = array_pop(\$this->values);
EOS;
    }
}
