<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Label implements ExpressionInterface
{
    private $name;
    private $expression;

    /**
     * @param string $name
     * @param ExpressionInterface $expression
     */
    public function __construct($name, ExpressionInterface $expression)
    {
        $this->name = $name;
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

        if ($result->isSuccess()) {
            $context->set($this->name, $result->getResult());
        }

        return $result;
    }
}
