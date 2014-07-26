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
        $hasResult = false;
        $result = '';

        while (true) {
            $attempt = $this->expression->parse($string, $context);

            if (!$attempt->isSuccess()) {
                break;
            }

            if (!$hasResult) {
                $result = $attempt->getResult();
            } elseif (is_string($result) && is_string($attempt->getResult())) {
                $result = $result . $attempt->getResult();
            } else {
                $result = array($result, $attempt->getResult());
            }

            $string = $attempt->getRest();
            $hasResult = true;
        }

        return new Success($result, $string);
    }
}
