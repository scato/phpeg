<?php

require_once 'Any.php';
require_once 'Choice.php';
//require_once 'Concat.php';
require_once 'Failure.php';
require_once 'Grammar.php';
require_once 'Input.php';
require_once 'Literal.php';
//require_once 'Many.php';
require_once 'Map.php';
require_once 'Proxy.php';
require_once 'RegExp.php';
require_once 'Success.php';
require_once 'Symbol.php';
//require_once 'Void.php';

$string = print_r(array('foo' => array(1, 2, 3)), true);

echo $string;
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

$value = new Choice(new Symbol($array, 'array'), new Any(new RegExp('[^\\n]')));

$keyValue = new Map(
    array(
        $lexical['TAB'],
        $lexical['BR_OPEN'],
        'key' => $lexical['KEY'],
        $lexical['BR_CLOSE'],
        $lexical['SPACE'],
        $lexical['ARROW'],
        $lexical['SPACE'],
        'value' => new Symbol($value, 'value'),
        $lexical['NEWLINE'],
    ),
    '
        array(array($key => $value))
    '
);

$array = new Map(
    array(
        $lexical['ARRAY'],
        $lexical['NEWLINE'],
        $lexical['TAB'],
        $lexical['PAREN_OPEN'],
        $lexical['NEWLINE'],
        'elements' => new Any(new Symbol($keyValue, 'keyValue')),
        $lexical['TAB'],
        $lexical['PAREN_CLOSE'],
        $lexical['NEWLINE'],
    ),
    '
        $elements === null ? array() : array_reduce($elements, \'array_merge\', array())
    '
);

$parser = $array;

$input = new Input($string);
$output = $parser->parse($input);

if ($output instanceof Failure) {
    echo "Parse error: {$output->getMsg()}\n";
} elseif ($input->hasNext()) {
    echo "Unexpected end of string\n";
} else {
    var_dump($output->getValue());
}

//class ArrayParser
//{
//    function parse_array(Input $input)
//    {
//        global $source_array;
//
//        eval($source_array);
//
//        return $result;
//    }
//
//    function parse(Input $input)
//    {
//        return $this->parse_array($input);
//    }
//}

$grammar = new Grammar('ArrayParser', 'array', array(
    new Symbol($value, 'value'),
    new Symbol($keyValue, 'keyValue'),
    new Symbol($array, 'array'),
));

$source = $grammar->compile();

eval($source);

echo "\n$source\n\n";

$arrayParser = new ArrayParser();

var_dump($arrayParser->parse(new Input($string)));
