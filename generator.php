<?php

require_once 'Any.php';
require_once 'Choice.php';
require_once 'Concat.php';
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

$string = <<<EOS
head : (
    T_CLASS
    ( ! ( T_SPACE ? "{" ) . ) *
)
T_SPACE ? "{"
{
        array_merge(\$head, [[T_SPACE, "\\n", 1], "{"])
}
EOS;

//echo $string;

$grammar = new Grammar('DefinitionParser', 'start', array(
    new Symbol($start, 'start'),
    new Symbol($_, '_'),
    new Symbol($map, 'map'),
    new Symbol($labelParser, 'labelParser'),
    new Symbol($unlabeledParser, 'unlabeledParser'),
    new Symbol($expr, 'expr'),

    new Symbol($sequences, 'sequences'),
    new Symbol($notPredicate, 'notPredicate'),
    new Symbol($repetition, 'repetition'),
    new Symbol($terminal, 'terminal'),
));

$_ = new RegExp('\\s*');

$start = new Map(array(
    new Symbol($_, '_'),
    'map' => new Symbol($map, 'map'),
    new Symbol($_, '_'),
), '$map');

$map = new Map(array(
    'parts' => new Any(
        new Map(array(
            'part' => new Choice(
                new Symbol($labelParser, 'labelParser'),
                new Symbol($unlabeledParser, 'unlabeledParser')
            ),
            new Symbol($_, '_'),
        ), '$part')
    ),
    new Literal('{'),
    new Symbol($_, '_'),
    'expr' => new Symbol($expr, 'expr'),
    new Symbol($_, '_'),
    new Literal('}'),
), 'new Map($parts, $expr)');

$labelParser = new Map(array(
    'label' => new RegExp('[\\w_]+'),
    new Symbol($_, '_'),
    new Literal(':'),
    new Symbol($_, '_'),
    'parser' => new Symbol($notPredicate, 'notPredicate')
), 'array($label => $parser)');

$unlabeledParser = new Map(array(
    'parser' => new Symbol($notPredicate, 'notPredicate')
), 'array($parser)');

$expr = new Any(
    new Choice(
        new Choice(
            new RegExp('[^{}"]'),
            new RegExp('"(?:[^\\\\"]|\\\\.)*"')
        ),
        new Concat(new Concat(new Literal("{"), new Symbol($expr, 'expr')), new Literal("}"))
    )
);

$sequences = new Map(array(
    'first' => new Symbol($notPredicate, 'notPredicate'),
    'rest' => new Any(new Map(array(
        new Symbol($_, '_'),
        'predicate' => new Symbol($notPredicate, 'notPredicate'),
    ), 'array($predicate)')),
), 'array_reduce($rest, function ($left, $right) { return new Sequence($left, $right); }, $first)');

$notPredicate = new Choice(
    new Map(array(
        new Literal('!'),
        new Symbol($_, '_'),
        'repetition' => new Symbol($repetition, 'repetition'),
    ), 'new NotPredicate($repetition)'),
    new Symbol($repetition, 'repetition')
);

$repetition = new Choice(
    new Choice(
        new Map(array(
            'terminal' => new Symbol($terminal, 'terminal'),
            new Symbol($_, '_'),
            new Literal('?'),
        ), 'new Optional($terminal)'),
        new Map(array(
            'terminal' => new Symbol($terminal, 'terminal'),
            new Symbol($_, '_'),
            new Literal('*'),
        ), 'new Any($terminal)')
    ),
    new Symbol($terminal, 'terminal')
);

$terminal = new Choice(
    new Choice(
        new Choice(
            new Map(array(
                new Literal('('),
                new Symbol($_, '_'),
                'parser' => new Symbol($sequences, 'sequences'),
                new Symbol($_, '_'),
                new Literal(')'),
            ), '$parser'),
            new Map(array(
                'type' => new RegExp('T_[A-Z]+'),
            ), 'new Type(constant($type))')
        ),
        new Map(array(
            'literal' => new RegExp('"(?:[^\\\\"]|\\\\.)*"'),
        ), 'new Literal($literal)')
    ),
    new Map(array(
        new Literal('.'),
    ), 'new Match(".*")')
);

//$output = $start->parse(new Input($string));
//print_r($output);

$source = $grammar->compile();
echo "<?php\n";
echo $source;

//eval($source);
//$generator = new Generator();

//$output2 = $generator->parse(new Input($string));
//print_r($output2);

