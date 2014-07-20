<?php

namespace PHPeg;

interface GrammarInterface
{
    /**
     * @param $name
     * @return ExpressionInterface
     */
    public function getRule($name);
} 
