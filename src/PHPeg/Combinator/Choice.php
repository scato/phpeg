<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Choice implements ExpressionInterface
{
    /**
     * @var ExpressionInterface[]
     */
    private $expressions;

    public function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * @param string $string
     * @param ContextInterface $context
     * @return ResultInterface
     */
    public function parse($string, ContextInterface $context)
    {
        foreach ($this->expressions as $expression) {
            $attempt = $expression->parse($string, $context);

            if ($attempt->isSuccess()) {
                return $attempt;
            }
        }

        return new Failure();
    }
}
