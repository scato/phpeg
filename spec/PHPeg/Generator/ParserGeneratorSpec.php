<?php

namespace spec\PHPeg\Generator;

use PHPeg\Grammar\PegFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserGeneratorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new PegFile());
    }

    function it_should_generate_a_tree()
    {
        $tree = $this->createTree(__DIR__ . '/PegFile.peg');

        $tree->shouldHaveType('PHPeg\Grammar\Tree\GrammarNode');
    }

    function it_should_generate_a_parser()
    {
        $tree = $this->createTree(__DIR__ . '/PegFile.peg');
        $parser = $this->createParser(__DIR__ . '/PegFile.peg');

        $parser->shouldHaveType($tree->getQualifiedName());
    }

    function it_should_generate_a_parser_that_parses_its_own_definition()
    {
        $parser = $this->createParser(__DIR__ . '/PegFile.peg');
        $definition = file_get_contents(__DIR__ . '/PegFile.peg');

        $tree = $parser->parse($definition);

        $tree->shouldHaveType('PHPeg\Grammar\Tree\GrammarNode');
    }

    function it_should_generate_a_parser_that_when_parsing_its_own_definition_results_in_the_same_tree()
    {
        $tree = $this->createTree(__DIR__ . '/PegFile.peg');
        $parser = $this->createParser(__DIR__ . '/PegFile.peg');
        $definition = file_get_contents(__DIR__ . '/PegFile.peg');

        $parser->parse($definition)->shouldBeLike($tree);
    }
}
