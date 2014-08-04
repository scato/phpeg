<?php


namespace PHPeg\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('input-file', InputArgument::REQUIRED);
        $this->addArgument('output-file', InputArgument::REQUIRED);
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

        file_put_contents($outputFile, 'TODO');
    }
}
