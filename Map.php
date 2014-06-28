<?php

require_once 'Parser.php';
require_once 'Compiler.php';

class Map implements Parser, Compiler
{
    public function __construct($parts, $callback)
    {
        $this->parts = $parts;
        $this->callback = $callback;
    }

    public function parse(Input $input)
    {
        $args = array();
        $params = array();

        foreach ($this->parts as $label => $right) {
            $result = $right->parse($input);

            if ($result instanceof Failure) {
                return $result;
            }

            if (is_string($label)) {
                $args[] = "\$$label";
                $params[] = $result->getValue();
            }
        }

        $func = create_function(implode(', ', $args), 'return ' . $this->callback . ';');

        return new Success(call_user_func_array($func, $params));
    }

    private static $id = 0;

    public function compile()
    {
        $id = self::$id++;

        $source = '
            $failed_map_' . $id . ' = false;
        ';

        foreach ($this->parts as $label => $right) {
            $source .= '
                if (!$failed_map_' . $id . ') {
                    ' . $right->compile() . '

                    if ($result instanceof Failure) {
                        $failed_map_' . $id . ' = true;
            ';

            if (is_string($label)) {
                $source .= '
                    } else {
                        $' . $label . ' = $result->getValue();
                ';
            }

            $source .= '
                    }
                }
            ';
        }

        $source .= '
            if (!$failed_map_' . $id . ') {
                $result = new Success(' . $this->callback . ');
            }
        ';

        return $source;
    }
}
