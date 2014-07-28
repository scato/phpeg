<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class MatchedString implements ExpressionInterface
{
    private $expression;

    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @param string $string
     * @param ContextInterface $context
     * @return ResultInterface
     */
    public function parse($string, ContextInterface $context)
    {
        $result = $this->expression->parse($string, $context);

        if (!$result->isSuccess()) {
            return $result;
        }

        $matched = substr($string, 0, strlen($string) - strlen($result->getRest()));

        return new Success($matched, $result->getRest());
    }
}
