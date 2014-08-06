<?php

namespace spec\PHPeg\Generator;

use PHPeg\Combinator\Grammar;
use PHPeg\Generator\ToCombinatorVisitor;
use PHPeg\Grammar\BinaryRuleFactory;
use PHPeg\Grammar\GrammarRuleFactory;
use PHPeg\Grammar\Parser;
use PHPeg\Grammar\ParserFactory;
use PHPeg\Grammar\TerminalRuleFactory;
use PHPeg\Grammar\UnaryRuleFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserGeneratorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new ParserFactory(
            new TerminalRuleFactory(),
            new UnaryRuleFactory(),
            new BinaryRuleFactory(),
            new GrammarRuleFactory()
        ));
    }

    function it_should_generate_a_parser_that_parses_its_own_definition()
    {
        $parser = $this->createParser(__DIR__ . '/PHPeg.peg');
        $contents = file_get_contents(__DIR__ . '/PHPeg.peg');

        for ($i = 0; $i < 100; $i++) {
            $parser->parse($contents)->shouldHaveType('PHPeg\Grammar\Tree\GrammarNode');

            return;
        }
    }

    function it_should_generate_a_parser_that_when_parsing_its_own_definition_results_in_the_same_parser()
    {
        $contents = file_get_contents(__DIR__ . '/PHPeg.peg');
        $firstGenerationParser = $this->createParser(__DIR__ . '/PHPeg.peg');
        $firstGenerationTree = $firstGenerationParser->parse($contents);

        $secondGenerationGrammar = new Grammar();
        $firstGenerationTree->getWrappedObject()->accept(new ToCombinatorVisitor($secondGenerationGrammar));
        $secondGenerationParser = new Parser($secondGenerationGrammar);
        $secondGenerationTree = $secondGenerationParser->parse($contents);

        $firstGenerationTree->shouldBeLike($secondGenerationTree);
    }
}
