<?php

namespace PHPeg\Generator;

use PHPeg\Combinator\Grammar;
use PHPeg\Grammar\Parser;
use PHPeg\Grammar\ParserFactory;

class ParserGenerator
{
    private $parserFactory;

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }
    public function createParser($filename)
    {
        $contents = file_get_contents($filename);
        $parser = $this->parserFactory->createParser();
        $tree = $parser->parse($contents);

        $grammar = new Grammar();
        $tree->accept(new ToCombinatorVisitor($grammar));
        return new Parser($grammar);
    }
}
