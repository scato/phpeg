<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class CharacterClass implements ExpressionInterface
{
    private $expression;

    public function __construct($expression)
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
        $result = substr($string, 0, 1);
        $rest = substr($string, 1);

        if (preg_match('/[' . $this->expression . ']/', $result)) {
            return new Success($result, $rest);
        }

        return new Failure();
    }
}
