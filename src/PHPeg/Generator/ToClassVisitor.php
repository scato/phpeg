<?php

namespace PHPeg\Generator;

use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\AndPredicateNode;
use PHPeg\Grammar\Tree\AnyNode;
use PHPeg\Grammar\Tree\CharacterClassNode;
use PHPeg\Grammar\Tree\ChoiceNode;
use PHPeg\Grammar\Tree\CutNode;
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
    private $unique = 1;

    private function id($prefix)
    {
        return '$_' . $prefix . $this->unique++;
    }

    private function indent($string)
    {
        return preg_replace('/(?<=\\n)(?!\\n)/', '    ', $string);
    }

    private function toSingleQuotes($expression)
    {
        return var_export($expression, true);
    }

    private function toDoubleQuotes($expression)
    {
        $search = array("\\", "\n", "\r", "\t", "\v", "\e", "\f", "\$", "\"");
        $replace = array('\\\\', '\\n', '\\r', '\\t', '\\v', '\\e', '\\f', '\\$', '\\"');

        return '"' . str_replace($search, $replace, $expression) . '"';
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
        $position = $this->id('position');

        $this->results[] = <<<EOS
{$position} = \$this->position;

{$this->getResult()}

if (\$_success) {
    \$this->value = null;
}

\$this->position = {$position};
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
        $pattern = $this->toSingleQuotes("/^[{$node->getString()}]$/");

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
        $position = $this->id('position');
        $cut = $this->id('cut');

        $pieces = $this->getResults($node->getLength());

        $result = <<<EOS
{$position} = \$this->position;
{$cut} = \$this->cut;

\$this->cut = false;
{$pieces[0]}
EOS;

        foreach (array_slice($pieces, 1) as $piece) {
            $result .= <<<EOS


if (!\$_success && !\$this->cut) {
    \$this->position = {$position};

    {$this->indent($piece)}
}
EOS;
        }

        $result .= <<<EOS


\$this->cut = {$cut};
EOS;

        $this->results[] = $result;
    }

    public function visitCut(CutNode $node)
    {
        $this->results[] = <<<EOS
\$_success = true;
\$this->value = null;

\$this->cut = true;
EOS;
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

        if ($node->getBase() === null) {
            $result .= <<<EOS
class {$node->getName()}
{
    protected \$string;
    protected \$position;
    protected \$value;
    protected \$cut = false;
    protected \$cache;
    protected \$errors = array();
    protected \$warnings = array();

EOS;
        } else {
            $result .= <<<EOS
class {$node->getName()} extends {$node->getBase()}
{
EOS;
        }

        $pieces = $this->getResults($node->getLength());

        foreach ($pieces as $piece) {
            $result .= <<<EOS

    {$this->indent($piece)}

EOS;
        }

        if ($node->getStartSymbol() !== null) {
            $result .= <<<EOS

    private function line()
    {
        return count(explode("\\n", substr(\$this->string, 0, \$this->position)));
    }

    private function rest()
    {
        return '\\'' . substr(\$this->string, \$this->position) . '\\'';
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

        \$_success = \$this->parse{$node->getStartSymbol()}();

        if (!\$_success) {
            throw new \InvalidArgumentException("Syntax error, expecting {\$this->expecting()} on line {\$this->line()}");
        }

        if (\$this->position < strlen(\$this->string)) {
            throw new \InvalidArgumentException("Syntax error, unexpected {\$this->rest()} on line {\$this->line()}");
        }

        return \$this->value;
    }

EOS;
        }

        $result .= <<<EOS
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
        $doubleQuotes = $this->toDoubleQuotes($node->getString());
        $doubleQuotesEscaped = $this->toDoubleQuotes($doubleQuotes);

        $this->results[] = <<<EOS
if (substr(\$this->string, \$this->position, {$strlen}) === {$doubleQuotes}) {
    \$_success = true;
    \$this->value = {$doubleQuotes};
    \$this->position += {$strlen};
} else {
    \$_success = false;

    \$this->report(\$this->position, {$doubleQuotesEscaped});
}
EOS;
    }

    public function visitMatchedString(MatchedStringNode $node)
    {
        $position = $this->id('position');

        $this->results[] = <<<EOS
{$position} = \$this->position;

{$this->getResult()}

if (\$_success) {
    \$this->value = strval(substr(\$this->string, {$position}, \$this->position - {$position}));
}
EOS;
    }

    public function visitNotPredicate(NotPredicateNode $node)
    {
        $position = $this->id('position');

        $this->results[] = <<<EOS
{$position} = \$this->position;

{$this->getResult()}

if (!\$_success) {
    \$_success = true;
    \$this->value = null;
} else {
    \$_success = false;
}

\$this->position = {$position};
EOS;
    }

    public function visitOneOrMore(OneOrMoreNode $node)
    {
        $position = $this->id('position');
        $value = $this->id('value');
        $cut = $this->id('cut');

        $result = $this->getResult();

        $this->results[] = <<<EOS
{$result}

if (\$_success) {
    {$value} = array(\$this->value);
    {$cut} = \$this->cut;

    while (true) {
        {$position} = \$this->position;

        \$this->cut = false;
        {$this->indent($this->indent($result))}

        if (!\$_success) {
            break;
        }

        {$value}[] = \$this->value;
    }

    if (!\$this->cut) {
        \$_success = true;
        \$this->position = {$position};
        \$this->value = {$value};
    }

    \$this->cut = {$cut};
}
EOS;
    }

    public function visitOptional(OptionalNode $node)
    {
        $position = $this->id('position');
        $cut = $this->id('cut');

        $this->results[] = <<<EOS
{$position} = \$this->position;
{$cut} = \$this->cut;

\$this->cut = false;
{$this->getResult()}

if (!\$_success && !\$this->cut) {
    \$_success = true;
    \$this->position = {$position};
    \$this->value = null;
}

\$this->cut = {$cut};
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
        \$this->report(\$_position, '{$node->getName()}');
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
        $value = $this->id('value');

        $pieces = $this->getResults($node->getLength());

        $result = <<<EOS
{$value} = array();

{$pieces[0]}
EOS;

        foreach (array_slice($pieces, 1) as $piece) {
            $result .= <<<EOS


if (\$_success) {
    {$value}[] = \$this->value;

    {$this->indent($piece)}
}
EOS;
        }

        $result .= <<<EOS


if (\$_success) {
    {$value}[] = \$this->value;

    \$this->value = {$value};
}
EOS;

        $this->results[] = $result;
    }

    public function visitZeroOrMore(ZeroOrMoreNode $node)
    {
        $position = $this->id('position');
        $value = $this->id('value');
        $cut = $this->id('cut');

        $this->results[] = <<<EOS
{$value} = array();
{$cut} = \$this->cut;

while (true) {
    {$position} = \$this->position;

    \$this->cut = false;
    {$this->indent($this->getResult())}

    if (!\$_success) {
        break;
    }

    {$value}[] = \$this->value;
}

if (!\$this->cut) {
    \$_success = true;
    \$this->position = {$position};
    \$this->value = {$value};
}

\$this->cut = {$cut};
EOS;
    }
}
