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

        $copy = $input->copy();
        $right = $this->right->parse($copy);

        if ($right instanceof Success) {
            $input->follow($copy);

            return $right;
        }

        return $left;
    }
}
