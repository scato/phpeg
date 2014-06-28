<?php

require_once 'Any.php';
require_once 'Choice.php';
require_once 'Concat.php';
require_once 'Failure.php';
require_once 'Input.php';
require_once 'Literal.php';
require_once 'Many.php';
require_once 'Map.php';
require_once 'Proxy.php';
require_once 'RegExp.php';
require_once 'Success.php';
require_once 'Void.php';

$input = print_r(array('foo' => array(1, 2, 3)), true);

echo $input;
echo "\n";

$lexical = array(
    'ARRAY' => new Literal('Array'),
    'NEWLINE' => new RegExp('\\n'),
    'TAB' => new Any(new Literal('    ')),
    'SPACE' => new Literal(' '),
    'PAREN_OPEN' => new Literal('('),
    'PAREN_CLOSE' => new Literal(')'),
    'BR_OPEN' => new Literal('['),
    'BR_CLOSE' => new Literal(']'),
    'ARROW' => new Literal('=>'),
    'KEY' => new RegExp('[^\\]]*'),
);

$value = new Choice(new Proxy($array), new Any(new RegExp('[^\\n]')));

$keyValue = new Map(
    array(
        $lexical['TAB'],
        $lexical['BR_OPEN'],
        'key' => $lexical['KEY'],
        $lexical['BR_CLOSE'],
        $lexical['SPACE'],
        $lexical['ARROW'],
        $lexical['SPACE'],
        'value' => $value,
        $lexical['NEWLINE'],
    ),
    function ($key, $value) {
        return array(array($key => $value));
    }
);

$array = new Map(
    array(
        $lexical['ARRAY'],
        $lexical['NEWLINE'],
        $lexical['TAB'],
        $lexical['PAREN_OPEN'],
        $lexical['NEWLINE'],
        'elements' => new Any($keyValue),
        $lexical['TAB'],
        $lexical['PAREN_CLOSE'],
        $lexical['NEWLINE'],
    ),
    function ($elements) {
        if ($elements === null) {
            return array();
        }

        return array_reduce($elements, 'array_merge', array());
    }
);

$parser = $array;

$input = new Input($input);
$output = $parser->parse($input);

if ($output instanceof Failure) {
    echo "Parse error: {$output->getMsg()}\n";
} elseif ($input->hasNext()) {
    echo "Unexpected end of string\n";
} else {
    var_dump($output->getValue());
}

$source = $lexical['TAB']->compile('test');

echo "\n$source\n\n";

function test(Input $input)
{
    global $source;

    eval($source);

    return $result;
}

var_dump(test(new Input('   ')));
var_dump(test(new Input('        ')));
