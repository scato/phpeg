<?php

require_once 'Parser.php';
require_once 'Compiler.php';

class Any implements Parser, Compiler
{
    public function __construct($left)
    {
        $this->left = $left;
    }

    public function parse(Input $input)
    {
        $result = new Success();

        while (true) {
            $copy = $input->copy();
            $left = $this->left->parse($copy);

            if ($left instanceof Failure) {
                return $result;
            }

            $result = $result->concat($left);
            $input->follow($copy);
        }
    }

    static $id = 0;

    public function compile()
    {
        $id = self::$id++;

        return '
            $result_any_' . $id . ' = new Success();

            while (true) {
                $copy_any_' . $id . ' = $input->copy();
                ' . $this->left->compile() . '

                if ($result instanceof Failure) {
                    $input->follow($copy_any_' . $id . ');
                    break;
                }

                $result_any_' . $id . ' = $result_any_' . $id . '->concat($result);
            }

            $result = $result_any_' . $id . ';
        ';
    }
}

