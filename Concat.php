<?php

require_once 'Parser.php';

class Concat implements Parser
{
    public function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function parse(Input $input)
    {
        $left = $this->left->parse($input);

        if ($left instanceof Failure) {
            return $left;
        }

        $right = $this->right->parse($input);

        if ($right instanceof Failure) {
            return $right;
        }

        return $left->concat($right);
    }

    public static function all()
    {
        if (count(func_get_args()) === 1) {
            return func_get_args()[0];
        } else {
            return new Concat(func_get_args()[0], call_user_func_array(array('Concat', 'all'), array_slice(func_get_args(), 1)));
        }
    }
}
