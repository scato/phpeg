<?php


namespace PHPeg\Console;

use PHPeg\Generator\ParserGenerator;
use PHPeg\Grammar\PegFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BenchmarkCommand extends Command
{
    private function humanReadable($value)
    {
        $units = 'BKMGT';
        $order = 0;

        while ($value >= 1024) {
            $value /= 1024;
            $order += 1;
        }

        $precision = 3 - strlen(floor($value));

        return round($value, $precision) . $units[$order];
    }

    protected function configure()
    {
        $this->setDescription('Generate and run a parser to evaluate its performance');
        $this->addArgument('input-file', InputArgument::REQUIRED, 'The grammar definition');
        $this->addArgument('example-file', InputArgument::REQUIRED, 'An example file to parse');
        $this->addOption('number', null, InputArgument::OPTIONAL, 'The number of times that the parser should be run', 10);
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
        $exampleFile = $input->getArgument('example-file');
        $number = $input->getOption('number');

        $parserGenerator = new ParserGenerator(new PegFile());

        $parser = $parserGenerator->createParser($inputFile);
        $contents = file_get_contents($exampleFile);
        $start = microtime(true);
        $memoryUsage = $memoryBase = memory_get_usage();

        for ($i = 0; $i < $number; $i++) {
            $parser->parse($contents);

            if ($i === 0) {
                $memoryUsage = memory_get_usage() - $memoryBase;
            }
        }

        $memory = $this->humanReadable($memoryUsage);
        $time = microtime(true) - $start;
        $ms = round($time * 1000);
        $avg = round($time * 1000 / $number);
        $rps = round($number / $time);

        $output->writeln("Memory usage: {$memory}");
        $output->writeln("Number of runs: {$number}");
        $output->writeln("Total time: {$ms}ms");
        $output->writeln("Average time: {$avg}ms");
        $output->writeln("Runs per second: {$rps}");
    }
}
