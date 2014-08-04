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

    private function createTree($filename)
    {
        $contents = file_get_contents($filename);
        $parser = $this->parserFactory->createParser();
        return $parser->parse($contents);
    }

    private function createClassFromTree($tree)
    {
        $visitor = new ToClassVisitor();
        $tree->accept($visitor);

        return $visitor->getResult();
    }

    public function createClass($filename)
    {
        $tree = $this->createTree($filename);

        return $this->createClassFromTree($tree);
    }

    public function createParser($filename)
    {
        $tree = $this->createTree($filename);
        $class = $tree->getName();

        if (!class_exists($class)) {
            eval($this->createClassFromTree($tree));
        }

        return new $class();
    }
}
