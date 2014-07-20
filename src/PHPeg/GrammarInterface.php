<?php

namespace PHPeg;

interface GrammarInterface
{
    /**
     * @param string $name
     * @return ExpressionInterface
     */
    public function getRule($name);

    /**
     * @param string $string
     * @return mixed
     */
    public function parse($string);
} 
