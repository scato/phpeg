<?php


namespace PHPeg;


interface ContextInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value);
} 
