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

$match = new Map(
    array(
        'match' => new Literal('    ')
    ),
    '\'....\''
);

$parser = new Any(new Choice($match, new RegExp('.|\\n')));

echo $parser->parse(new Input($string))->getValue();

