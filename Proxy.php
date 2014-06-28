<?php

require_once 'Parser.php';

class Proxy implements Parser
{
    public function __construct(&$parser)
    {
        $this->parser =& $parser;
    }

    public function parse(Input $input)
    {
        return $this->parser->parse($input);
    }
}
