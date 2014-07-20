<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;

class Choice implements ExpressionInterface
{
    private $left;
    private $right;

    public function __construct(ExpressionInterface $left, ExpressionInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @param string $string
     * @return \PHPeg\ResultInterface
     */
    public function parse($string)
    {
        $left = $this->left->parse($string);

        if ($left->isSuccess()) {
            return $left;
        }

        $right = $this->right->parse($string);

        if ($right->isSuccess()) {
            return $right;
        }

        return new Failure();
    }
}
