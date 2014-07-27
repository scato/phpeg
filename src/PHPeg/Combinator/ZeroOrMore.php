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
        $result = array();

        while (true) {
            $attempt = $this->expression->parse($string, $context);

            if (!$attempt->isSuccess()) {
                break;
            }

            $result[] = $attempt->getResult();
            $string = $attempt->getRest();
        }

        return new Success($result, $string);
    }
}
