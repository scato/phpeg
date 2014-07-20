<?php

require_once 'Any.php';
require_once 'Choice.php';
require_once 'Concat.php';
require_once 'Failure.php';
require_once 'Grammar.php';
require_once 'Input.php';
require_once 'Literal.php';
require_once 'Many.php';
require_once 'Map.php';
require_once 'Proxy.php';
require_once 'RegExp.php';
require_once 'Success.php';
require_once 'Symbol.php';
require_once 'Void.php';

$string = 'x + 1 * 2 - 3 / (4 - 5)';

$grammar = new Grammar('ExprParser', 'start', array(
    new Symbol($start, 'start'),
    new Symbol($add, 'add'),
    new Symbol($mul, 'mul'),
    new Symbol($term, 'term'),
    new Symbol($sym, 'sym'),
    new Symbol($lit, 'lit'),
    new Symbol($_, '_'),
));

$_ = new RegExp('\\s*');

$start = new Map(array(
    new Symbol($_, '_'),
    'add' => new Symbol($add, 'add'),
    new Symbol($_, '_'),
), '$add');

$add = new Map(array(
    'first' => new Symbol($mul, 'mul'),
    'rest' => new Any(new Map(array(
        new Symbol($_, '_'),
        'op' => new Choice(new Literal('+'), new Literal('-')),
        new Symbol($_, '_'),
        'mul' => new Symbol($mul, 'mul'),
    ), 'array(array($op, $mul))')),
), 'array_reduce($rest === null ? array() : $rest, function ($left, $right) { return array($right[0], $left, $right[1]); }, $first)');

$mul = new Map(array(
    'first' => new Symbol($term, 'term'),
    'rest' => new Any(new Map(array(
        new Symbol($_, '_'),
        'op' => new Choice(new Literal('*'), new Literal('/')),
        new Symbol($_, '_'),
        'term' => new Symbol($term, 'term'),
    ), 'array(array($op, $term))')),
), 'array_reduce($rest === null ? array() : $rest, function ($left, $right) { return array($right[0], $left, $right[1]); }, $first)');

$term = new Choice(
    new Choice(
        new Symbol($sym, 'sym'),
        new Symbol($lit, 'lit')
    ),
    new Map(array(
        new Literal("("),
        new Symbol($_, '_'),
        'add' => new Symbol($add, 'add'),
        new Symbol($_, '_'),
        new Literal(")"),
    ), '$add')
);

$sym = new RegExp('[a-z]+');

$lit = new RegExp('[0-9]+');

$num = 10000;

$begin = time();
for ($i = 0; $i < $num; $i++) {
    $output = $start->parse(new Input($string));
}
echo "time: " . (time() - $begin) . "s\n";
echo "rps: " . round($num / (time() - $begin)) . "\n";
echo "\n";

$source = $grammar->compile();
eval($source);
$exprParser = new ExprParser();

$begin = time();
for ($i = 0; $i < $num; $i++) {
    $grammar->compile();
    $output = $exprParser->parse(new Input($string));
}
echo "time: " . (time() - $begin) . "s\n";
echo "rps: " . round($num / (time() - $begin)) . "\n";
echo "\n";

$begin = time();
for ($i = 0; $i < $num; $i++) {
    $output = $exprParser->parse(new Input($string));
}
echo "time: " . (time() - $begin) . "s\n";
echo "rps: " . round($num / (time() - $begin)) . "\n";
echo "\n";

