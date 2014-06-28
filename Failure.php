<?php

class Failure
{
    public function __construct($msg)
    {
        $this->msg = $msg;
    }

    public function getMsg()
    {
        return $this->msg;
    }
}
