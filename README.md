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
$ bin/phpeg generate <input> <output>
```

About
-----

This is a PEG parser generator. It used to contain combinator parsing, but now it contains only code generation.

The generated parser is a PEG parser that uses memoization. This effectively makes it a Packrat Parser, or so I'm told.
