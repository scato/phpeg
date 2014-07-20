<?php


namespace PHPeg;


interface ExpressionInterface
{
    /**
     * @param string $string
     * @param ContextInterface $context
     * @return ResultInterface
     */
    public function parse($string, ContextInterface $context);
} 
