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
                    $output = $this->parse_' . $this->start . '($input);

                    if ($input->hasNext()) {
                        return new Failure("Unexpected \'" . $input->next() . "\' at " . $input->at());
                    }

                    return $output;
                }
            }
        ';

        return $source;
    }
} 
