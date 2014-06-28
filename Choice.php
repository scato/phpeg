<?php

require_once 'Parser.php';

class Choice implements Parser
{
    public function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function parse(Input $input)
    {
        $copy = $input->copy();
        $left = $this->left->parse($copy);

        if ($left instanceof Success) {
            $input->follow($copy);

            return $left;
        }

        $right = $this->right->parse($input);

        return $right;
    }

    private static $id = 0;

    public function compile()
    {
        $id = self::$id++;

        return '
            $copy_choice_' . $id . ' = $input->copy();
            ' . $this->left->compile() . '

            if ($result instanceof Failure) {
                $input->follow($copy_choice_' . $id . ');
                ' . $this->right->compile() . '
            }
        ';
    }
}
