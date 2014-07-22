<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Action implements ExpressionInterface
{
    private $expression;
    private $code;

    public function __construct(ExpressionInterface $expression, $code)
    {
        $this->expression = $expression;
        $this->code = $code;
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

        return new Success($context->evaluate($this->code), $result->getRest());
    }
}
