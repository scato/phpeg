PHPeg
=====

PEG Parser Generator

Warning! Work in progress!

Usage
-----

Install this tool as a dev requirement:

```
"require-dev": {
    "scato/phpeg": "*"
}
```

You can now generate (and regenerate) parsers using the command line tool:

```
$ bin/phpeg generate <input> [<output>]
```

Read [the documentation](doc/grammar.md) for help on the grammar syntax.

About
-----

PHPeg is a PEG parser generator.

The generated parser is a PEG parser that uses memoization. This effectively makes it a Packrat Parser, or so I'm told.

PHPeg is heavily inspired by [PEG.js](http://pegjs.majda.cz/). Read [the documentation](doc/grammar.md) for details
on the differences.
