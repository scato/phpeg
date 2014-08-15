<?php


namespace PHPeg\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    const VERSION = '0.1';

    public function __construct()
    {
        parent::__construct('PHPeg', self::VERSION);

        $this->setupCommands();
    }

    private function setupCommands()
    {
        $generate = new GenerateCommand('generate');
        $this->add($generate);

        $benchmark = new BenchmarkCommand('benchmark');
        $this->add($benchmark);
    }
}
