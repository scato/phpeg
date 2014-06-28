<?php

require_once 'Parser.php';
require_once 'Compiler.php';

class Literal implements Parser, Compiler
{
    public function __construct($str)
    {
        $this->str = $str;
        $this->len = strlen($str);
    }

    public function parse(Input $input)
    {
        $restStr = $input->restStr();

        if (substr($restStr, 0, $this->len) === $this->str) {
            $input->skip($this->len);

            return new Success($this->str);
        }

        return new Failure("Expected: '" . $this->str . "' at " . $input->at());
    }

    public function compile()
    {
        $str = var_export($this->str, true);
        $len = var_export($this->len, true);

        return '
        $restStr = $input->restStr();

        if (substr($restStr, 0, ' . $len . ') === ' . $str . ') {
            $input->skip(' . $len . ');

            $result = new Success(' . $str . ');
        } else {
            $result = new Failure("Expected: \'" . ' . $str . ' . "\' at " . $input->at());
        }
        ';
    }
}
