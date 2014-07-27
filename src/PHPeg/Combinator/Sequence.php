<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Sequence implements ExpressionInterface
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
        $result = array();

        foreach ($this->expressions as $expression) {
            $attempt = $expression->parse($string, $context);

            if (!$attempt->isSuccess()) {
                return $attempt;
            }

            $result[] = $attempt->getResult();
            $string = $attempt->getRest();
        }

        return new Success($result, $string);
    }
}
