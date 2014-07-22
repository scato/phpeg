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

    /**
     * @param string $code
     * @return mixed
     */
    public function evaluate($code)
    {
        $args = array();
        $params = array();

        foreach($this->values as $key => $value) {
            $args[] = "\$$key";
            $params[] = $value;
        }

        $callable = create_function(implode(', ', $args), $code);

        return call_user_func_array($callable, $params);
    }
}
