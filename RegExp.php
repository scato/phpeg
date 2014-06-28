<?php

require_once 'Parser.php';
require_once 'Compiler.php';

class RegExp implements Parser, Compiler
{
    public function __construct($exp)
    {
        $this->exp = $exp;
    }

    public function parse(Input $input)
    {
        if (preg_match("/^{$this->exp}/", $input->restStr(), $matches)) {
            $input->skip(strlen($matches[0]));

            return new Success($matches[0]);
        }

        return new Failure("Expected: /" . $this->exp . "/ at " . $input->at());
    }

    public function compile()
    {
        $fullExp = var_export("/^{$this->exp}/", true);
        $exp = var_export($this->exp, true);

        return '
        if (preg_match(' . $fullExp . ', $input->restStr(), $matches)) {
            $input->skip(strlen($matches[0]));

            $result = new Success($matches[0]);
        } else {
            $result = new Failure("Expected: /" . ' . $exp . ' . "/ at " . $input->at());
        }
        ';
    }
}
