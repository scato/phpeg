<?php


namespace PHPeg\Console;

use PHPeg\Generator\ParserGenerator;
use PHPeg\Grammar\PegFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Generate a parser class from a grammar definition file');
        $this->addArgument('input-file', InputArgument::REQUIRED, 'The grammar definition');
        $this->addArgument('output-file', InputArgument::OPTIONAL, 'The parse class file path');
        $this->setHelp(
<<<EOS
If no parse class file path is given, the grammar definition file path is used.

The extension with ".php". For example, if the grammar is in:

    src/PHPeg/Grammar/PegFile.peg

then the parser class will be written to:

    src/PHPeg/Grammar/PegFile.php

EOS
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = $input->getArgument('input-file');
        $outputFile = $input->getArgument('output-file');

        if ($outputFile === null) {
            $outputFile = preg_replace('/\\.[^.]*$/', '.php', $inputFile);
        }

        $parserGenerator = new ParserGenerator(new PegFile());

        $output = "<?php\n\n" . $parserGenerator->createClass($inputFile);

        file_put_contents($outputFile, $output);
    }
}
