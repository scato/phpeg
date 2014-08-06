<?php

namespace PHPeg\Generator;

use PHPeg\Grammar\PegFile;
use PHPeg\Grammar\Tree\GrammarNode;

class ParserGenerator
{
    private $parser;

    public function __construct(PegFile $parser)
    {
        $this->parser = $parser;
    }

    public function createTree($filename)
    {
        $definition = file_get_contents($filename);

        return $this->parser->parse($definition);
    }

    private function createClassFromTree(GrammarNode $tree)
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
        $class = $tree->getQualifiedName();

        if (!class_exists($class)) {
            eval($this->createClassFromTree($tree));
        }

        return new $class();
    }
}
