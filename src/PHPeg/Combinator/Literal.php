<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class Literal implements ExpressionInterface
{
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @param string $string
     * @param ContextInterface $context
     * @return ResultInterface
     */
    public function parse($string, ContextInterface $context)
    {
        $strlen = strlen($this->string);
        $result = substr($string, 0, $strlen);

        if ($result === $this->string) {
            $rest = substr($string, $strlen);

            if ($rest === false) {
                $rest = '';
            }

            return new Success($result, $rest);
        }

        return new Failure();
    }
}
