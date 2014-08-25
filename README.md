PHPeg
=====

PEG Parser Generator

This project is still unstable. Some features like case-insensitive literals are stil missing. The rest should work, though.

Usage
-----

Install this tool as a dev requirement:

```
"require-dev": {
    "scato/phpeg": "1.*"
}
```

You can now generate (and regenerate) parsers using the command line tool:

```
$ vendor/bin/phpeg generate <input-file> [<output-file>]
```

Read [the grammar section](doc/grammar.md) in the documentation for help on the grammar syntax. Read
[the usage section](doc/usage.md) for more details on both basic and advanced usage.

About
-----

PHPeg is a PEG parser generator.

The generated parser is a PEG parser that uses memoization. This effectively makes it a Packrat Parser, or so I'm told.

PHPeg is heavily inspired by [PEG.js](http://pegjs.majda.cz/). [The grammar section](doc/grammar.md) contains details
on the differences.
