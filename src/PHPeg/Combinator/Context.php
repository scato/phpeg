<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;

class Context implements ContextInterface
{
    private $values = array();

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->values[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value)
    {
        $this->values[$name] = $value;
    }
}
