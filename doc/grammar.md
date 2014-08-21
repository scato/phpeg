PHPeg Grammar Definition Syntax
===============================

Introduction
------------

The grammar definition syntax is heaviliy inspired by [PEG.js](http://pegjs.majda.cz/). The main differences are:

  - PHPeg includes a cut operator (^), which also influences error reporting
  - PHPeg does not support initializers
  - PHPeg does not support named rules

Unsupported features might be added in the future.

Grammar Definitions
-------------------

The grammar syntax is similar to PHP, since the generated parser should be PSR-1 compliant. Namespaces and imports
are optional but highly recommended. The recommended extension for grammar definition files is ".peg".

Each grammar definition file should define one grammar:

```
namespace Acme\Demo;

use Acme\Demo\Tree\FooNode;

grammar DemoFile
{
    start DemoFile = Demo { return new FooNode(); };

    Demo = "foo";
}
```

A top-level grammar should start with a start rule (a rule prefixed with the "start" keyword). Every rule must be
terminated by a semi-colon. ``StudlyCaps`` are recommended for rule identifiers, since they are prefixed with "parse"
in the generated code. The symbol ``_`` is usually used for layout characters like white space and comments. This can
then be inserted into non-terminal rules as follows:

```
grammar SumFile
{
    start SumFile = _ Sum _;

    Sum = Primary (_ "+" _ Primary)*;
    Primary = [0-9]+;
    _ = " "*;
}
```

Grammars are turned into classes and rules into methods. This makes it very simple to extend existing grammars:

```
grammar MathFile extends SumFile
{
    Sum = Factor (_ "+" _ Factor)*;
    Factor = Primary (_ "*" _ Primary)*;
}
```

Like PHP files, grammar files can contain inline comments (``//``) and block comments (``/* ... */``).

Parsing Expressions
-------------------

Parsing expressions look a lot like regular expressions. Parsing expressions can contain references to other rules,
making it possible to create recursive grammars. Also, parsing expressions have return values. They don't just match,
but transform the input as well.

``"literal"``  
``'literal'``

> Match an exact string and return it. These literals are directly copied to the parser. This means you can use
> variables for back-references: ``"</{$tagName}>"``. The difference between single quotes and double quotes is the
> same as in PHP, so ``'\n'`` will not match newlines.  
> Appending i right after the literal makes the match case-insensitive.

``.``

> Match any one character and return it as a string.

``[characters]``

> Match one character from a character class. The characters in the list can be escaped in exactly the same way
> as you would in a ``preg_match()`` pattern.

``^``

> The cut operator always succeeds and returns ``null``. The parser position is not advanced. If the parser fails after
> a cut operator, backtracking is suppressed for the last choice or repetition. For more information, see
> [Error Reporting](error-reporting.md).

> The cut operator is also a way to make your grammar consume less memory. (See
> [Mizushima](http://ialab.cs.tsukuba.ac.jp/~mizusima/publications/paste513-mizushima.pdf).)

``Rule``

> Match a parsing expression and return its match result.

``( expression )``

> Match a sub-expression and return its match result.

``expression *``

> Match zero or more repetitions of the expression and return their match results in an array. The match is greedy,
> i.e. the parser tries to match the expression as many times as possible. Once it fails, it backtracks to the end
> of the last successful match.

``expression +``

> Match one or more repetitions of the expression and return their match results in an array. Like the star operator,
> the plus operator is greedy.

``expression ?``

> Try to match the expression. If the match succeeds, return its match result, otherwise return ``null``.

``& expression``

> Try to match the expression. If the match succeeds, just return ``null`` and do not advance the parser position,
> otherwise consider the match failed.

``! expression``

> Try to match the expression. If the match does **not** succeed, return ``null`` and do not advance the parser
> position. If matching the expression **does** succeed, consider the match failed.

``& { action }``

> Evaluate the expression. If it evaluates to true, consider the match successful and return null. Otherwise, consider
> the match failed. Do not advance the parser position.

``! { action }``

> Evaluate the expression. If it evaluates to false, consider the match successful and return null. Otherwise, consider
> the match failed. Do not advance the parser position.

``$ expression``

> Try to match the expression. If the match succeeds, return the matched input string instead of the match result.

> Matched strings are useful for terminal expressions that are made up out of repetitions and sequences.

``label : expression``

> Match the expression and remember its match result under the given label. The label can be any valid PHP identifier,
> except that "start" is a reserved keyword in PHPeg. Also, names starting with "_" are discouraged, as these are
> used for internal parsing variables.

> Labeled expressions are useful in combination with actions, where saved match results can be accessed by the action's
> PHP code.

``expression_1 expression_2 ... expression_n``

> Match a sequence of expressions and return their match results in an array.

``expression { action }``

> Match the expression. If the match is successful, evaluate the action, otherwise consider the match failed.

> The action should be a piece of PHP code that gets put into a closure. All preceding labels are inherited (by
> reference) from the parent scope using the ``use`` language construct. The return value of the closure is used as
> the match result of the expression.

> You can also access the parser object using ``$this``, but this means your parser only works in PHP 5.4+.

> Also note that curly braces must be balanced, even if they are in a comment or a string literal.

``expression_1 / expression_2 / ... / expression_n``

> Try to match the first expression, if it does not succeed, try the second one, etc. Return the match result of the
> first successfully matched expression. If no expression matches, consider the match failed. Backtracking is done
> between attempts, so every expression is matched against the same starting position.

Compatibility
-------------

Both the parser generator and generated parsers should run on PHP 5.3+.
