Error Reporting and the Cut Operator
====================================

Basic Error Reporting
---------------------

PEGs are not especially good at error reporting. The parser PHPeg generates will tell you on which line the error
probably occurred, and what you could do to fix it. These suggestions are not always helpful.

For example, take the following grammar:

```
grammar SumFile
{
    start SumFile = _ Sum _;

    Sum = left:Primary (_ "+" _ right:Primary)*;
    Primary = $([0-9]+) / "(" _ Sum _ ")"

    _ = (" " / "\n")*;
}
```

If we feed the parser with ``"(1 + 2 + 3 + x)"`` we get the error:

```
Syntax error, expecting " ", "\n", "(", Primary on line 1
```

Named Rules
-----------

If a rule fails, an error will be reported using the rule's identifier. Rules can also have names, by inserting it
after the identifier:

```
grammar SumFile
{
    start SumFile = _ Sum _;

    Sum "expression" = left:Primary (_ "+" _ right:Primary)*;
    Primary "number" = $([0-9]+) / "(" _ Sum _ ")"

    _ = (" " / "\n")*;
}
```

The error above then changes to:

```
Syntax error, expecting " ", "\n", "(", number on line 1
```

The Cut Operator
----------------

PHPeg includes a cut operator. This operator is mainly meant for reducing memory consumption, but
[Mizushima](http://ialab.cs.tsukuba.ac.jp/~mizusima/publications/paste513-mizushima.pdf) also hints at a way to improve
error reporting.

Normally, if parsing fails somewhere within a choice (``/``) or a repetition (``*``, ``+`` or ``?``) the parser
backtracks to the next option or aborts the repetition. If the parser matches a cut, the parser switches to error mode.
If parsing fails during error mode, the nearest choice or repetition fails and the mode is reset to what it was before
the parser entered the choice or repetition.

Although Mizushima does not elaborate on improving error reporting, it turns out that if you give split failures into
warning (those that happened before a cut) and errors (those that happened after a cut) then errors are way more useful
than warnings.

For example, if we insert cuts as follows:
```
grammar SumFile
{
    start SumFile = _ Sum _;

    Sum = left:Primary (_ "+" ^ _ right:Primary)*;
    Primary = $([0-9]+) / "(" ^ _ Sum _ ")"

    _ = (" " / "\n")*;
}
```

Then the error turns into:

```
Syntax error, expecting Primary on line 1
```

Failure inside ``_`` is ignored because it's an option inside a repetition. Failure inside ``Primary`` is also ignored
because ``"("`` is in an option, before a cut. The failure of ``Primary`` itself is the only thing left to report as
an error, and is also the most relevant, because ``"x"`` just isn't a ``Primary``.

Because the cut operator prevents backtracking, you cannot just insert it anywhere. Inserting it after ``"+"`` makes
sense: there are no other uses of ``"+"``, so it should be followed by a valid primary expression. The same goes for
``"("``. This marks the start of a sub-expression which should always be finished.
