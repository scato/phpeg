<?php


namespace PHPeg;


interface ExpressionInterface
{
    /**
     * @param string $string
     * @return ResultInterface
     */
    public function parse($string);
} 
