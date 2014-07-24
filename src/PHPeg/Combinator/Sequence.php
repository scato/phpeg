<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Sequence implements ExpressionInterface
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
     * @param ContextInterface $context
     * @return ResultInterface
     */
    public function parse($string, ContextInterface $context)
    {
        $left = $this->left->parse($string, $context);

        if (!$left->isSuccess()) {
            return $left;
        }

        $right = $this->right->parse($left->getRest(), $context);

        if (!$right->isSuccess()) {
            return $right;
        }

        if (is_string($left->getResult()) && is_string($right->getResult())) {
            return new Success($left->getResult() . $right->getResult(), $right->getRest());
        }

        return new Success(array($left->getResult(), $right->getResult()), $right->getRest());
    }
}
