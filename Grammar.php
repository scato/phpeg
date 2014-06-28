<?php

require_once 'Compiler.php';

class Grammar implements Compiler
{
    public function __construct($name, $start, array $symbols)
    {
        $this->name = $name;
        $this->start = $start;
        $this->symbols = $symbols;
    }

    public function compile()
    {
        $source = '
            class ' . $this->name . '
            {
        ';

        foreach ($this->symbols as $symbol) {
            $source .= $symbol->compileMethod();
        }

        $source .= '
                public function parse(Input $input)
                {
                    return $this->parse_' . $this->start . '($input);
                }
            }
        ';

        return $source;
    }
} 
