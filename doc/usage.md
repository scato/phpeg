Basic and Advanced Usage
========================

Basic Usage
-----------

To generate your own parser, start with adding PHPeg as a dev requirement:

```
"require-dev": {
    "scato/phpeg": "1.*"
}
```

Run composer and get some coffee.

You can now start writing your grammar. I'd recommend putting the grammar in the same location as your code. For
example, the following file could be saved as ``src/Acme/Demo/Parser.peg``:

```
namespace Acme\Demo;

grammar Parser
{
}
```

Are you working test first? Of course you are! Remember, grammars are basically just really big regular expressions.
Mistakes can be hard to spot by looking at your grammar. Working test first prevents you from getting stuck with a
grammar that "just doesn't work".

BDD With PHPSpec
----------------

*This example uses PHPSpec, you could do this with PHPUnit just as well.*

Start by setting up ``composer.json``. This is what it could look like:

```
{
    "require-dev": {
        "scato/phpeg": "1.*",
        "phpspec/phpspec": "~2.1"
    },
    "autoload": {
        "psr-4": { "Acme\\": "src/Acme/" }
    }
}
```

After running ``composer install`` we can get started. We don't have a parser yet, we only have the grammar file.
We don't have a spec file either, so let's start with that:

```
$ vendor/bin/phpspec describe Acme/Demo/Parser
```

When we run it, PHPSpec will tell us that the parser does not exist. Don't let PHPSpec generate it for you. PHPeg
should do that:

```
$ vendor/bin/phpeg generate src/Acme/Demo/Parser.peg
```

Now, PHPSpec will succeed.

*There are a couple of different ways to continue. Whether mine is the right way is highly debatable.*

Next, start with the most simple input you'd like to parse. Suppose you'd like to parse graphs, start with the
GraphNode class that the parser should return:

```
$ vendor/bin/phpspec describe Acme/Demo/Tree/GraphNode
```

Once that's done, make sure you can parse an empty graph:

```
// spec/Acme/Demo/ParserSpec.php

function it_should_parse_an_empty_graph()
{
    $this->parse('')->shouldBeLike(new GraphNode());
}
```

Then, change your parser:

```
// src/Acme/Demo/Parser.peg

namespace Acme\Demo;

use Acme\Demo\Tree\GraphNode;

grammar Parser
{
    start Graph = .* { return new GraphNode(); };
}
```

And finally, run PHPeg to update the parser:

```
$ vendor/bin/phpeg generate src/Acme/Demo/Parser.peg
```

Now, PHPSpec should succeed again. Continue with a simple graph and keep adding node types and test cases until you
are done! Using the parser in your code is easy:

```
$parser = new \Acme\Demo\Parser();
$graphNode = $parser->parse($string);
```

If the input is invalid, an ``InvalidArgumentException`` is thrown. See the section on
[error reporting](error-reporting.md) for hints on how to make the parser generate useful error messages.

Advanced Usage
--------------

Once your grammar is finished, you might want to tweak it for speed and memory. PHPeg has a command that let's you
benchmark your parser:

```
$ vendor/bin/phpeg benchmark src/Acme/Demo/Parser.peg example.txt
Memory usage: 4.42M
Number of runs: 10
Total time: 918ms
Average time: 92ms
Runs per second: 11
```

Adding guard clauses like can improve you grammar a lot. For example, PegFile.peg used to have a rule:

```
_ = (Whitespace / BlockComment / InlineComment)*;
```

Now it reads:

```
_ = (&[ \n\r\t\/] (Whitespace / BlockComment / InlineComment))*;
```

After each whitespace, these three rules were called to check for more whitespace or a comment. Since both type of
comments start with a slash, it's very easy to check whether the parser should look for one.

