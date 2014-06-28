<?php

require_once 'Parser.php';

class Map implements Parser
{
    public function __construct($parts, $callback)
    {
        $this->parts = $parts;
        $this->callback = $callback;
    }

    public function parse(Input $input)
    {
        $results = array();

        foreach ($this->parts as $label => $right) {
            $result = $right->parse($input);

            if ($result instanceof Failure) {
                return $result;
            }

            if (is_string($label)) {
                $results[$label] = $result->getValue();
            }
        }

        return new Success(call_user_func_array($this->callback, $results));
    }
}
