<?php

namespace PHPeg\Combinator;

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
     * @return ResultInterface
     */
    public function parse($string)
    {
        $result = new Success('', $string);

        while (true) {
            $attempt = $this->expression->parse($string);

            if (!$attempt->isSuccess()) {
                break;
            }

            $result = new Success($result->getResult() . $attempt->getResult(), $attempt->getRest());
            $string = $result->getRest();
        }

        return $result;
    }
}
