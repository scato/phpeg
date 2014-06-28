<?php

require_once 'Parser.php';

class Void implements Parser
{
    public function parse(Input $input)
    {
        return new Success();
    }
}
