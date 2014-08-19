<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
require_once 'ApplicationContext.php';
require_once 'WorkDirContext.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    use ApplicationContext;
    use WorkDirContext;

    protected function createApplication()
    {
        $parameterBag = new \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag();
        $container = new \Symfony\Component\DependencyInjection\Container($parameterBag);

        return new \PHPeg\Console\Application($container);
    }

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^I have a grammar containing "([^"]*)"$/
     */
    public function iHaveAGrammarContaining($contents)
    {
        $this->inputFile = tempnam('.', 'phpeg');

        file_put_contents($this->inputFile, $contents);
    }

    /**
     * @Given /^I have a grammar containing:$/
     */
    public function iHaveAGrammarContaining2(PyStringNode $string)
    {
        $this->inputFile = tempnam('.', 'phpeg');

        file_put_contents($this->inputFile, $string->getRaw());
    }

    /**
     * @Then /^my class should contain:$/
     */
    public function myClassShouldContain(PyStringNode $string)
    {
        assertContains($string->getRaw(), file_get_contents($this->outputFile));
    }

    private $result;
    private $error;

    /**
     * @When /^I parse "([^"]*)"$/
     */
    public function iParse($string)
    {
        $parserGenerator = new \PHPeg\Generator\ParserGenerator(new \PHPeg\Grammar\PegFile());
        $parser = $parserGenerator->createParser($this->inputFile);

        $this->result = null;
        $this->error = null;

        try {
            $this->result = $parser->parse($string);
        } catch (InvalidArgumentException $exception) {
            $this->error = $exception;
        }
    }

    /**
     * @Then /^I get (?!an error|the error)(.*)$/
     */
    public function iGet($result)
    {
        if ($this->error !== null) {
            throw $this->error;
        }

        assertSame(json_decode($result), $this->result);
    }

    /**
     * @Then /^I get an error$/
     */
    public function iGetAnError()
    {
        assertNotNull($this->error);
    }

    /**
     * @Then /^I get the error "(.*)"$/
     */
    public function iGetTheError($error)
    {
        assertNotNull($this->error);
        assertEquals($error, $this->error->getMessage());
    }
}
