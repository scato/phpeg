<?php

namespace PHPeg\Combinator;

use PHPeg\ExpressionInterface;

class Literal implements ExpressionInterface
{
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @param string $string
     * @return \PHPeg\ResultInterface
     */
    public function parse($string)
    {
        $strlen = strlen($this->string);
        $result = substr($string, 0, $strlen);

        if ($result === $this->string) {
            $rest = substr($string, $strlen);

            return new Success($result, $rest);
        }

        return new Failure();
    }
}
