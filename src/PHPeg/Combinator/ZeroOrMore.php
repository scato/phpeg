<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class ZeroOrMore implements ExpressionInterface
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
        $result = new Success('', $string);

        while (true) {
            $attempt = $this->expression->parse($string, $context);

            if (!$attempt->isSuccess()) {
                break;
            }

            $result = new Success($result->getResult() . $attempt->getResult(), $attempt->getRest());
            $string = $result->getRest();
        }

        return $result;
    }
}
