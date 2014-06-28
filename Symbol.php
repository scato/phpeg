<?php

require_once 'Compiler.php';

class Symbol extends Proxy implements Compiler
{
    public function __construct(&$parser, $name)
    {
        parent::__construct($parser);

        $this->name = $name;
    }

    public function compileMethod()
    {
        return '
            protected function parse_' . $this->name . '(Input $input)
            {
                ' . $this->parser->compile() . '

                return $result;
            }
        ';
    }

    public function compile()
    {
        return '
            $result = $this->parse_' . $this->name . '($input);
        ';
    }
} 
