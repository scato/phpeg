<?php

class Success
{
    public function __construct($val = null)
    {
        $this->val = $val;
    }

    public function getValue()
    {
        return $this->val;
    }

    public function concat($right)
    {
        if (is_null($this->val)) {
            return $right;
        }

        if (is_null($right->val)) {
            return $this;
        }

        if (is_string($this->val) && is_string($right->val)) {
            return new Success($this->val . $right->val);
        }

        if (is_array($this->val) && is_array($right->val)) {
            return new Success(array_merge($this->val, $right->val));
        }

        throw new RuntimeException("Type mismatch");
    }
}
