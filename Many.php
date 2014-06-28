<?php

require_once 'Proxy.php';

class Many extends Proxy
{
    public function __construct($left)
    {
        parent::__construct(new Concat($left, new Any($left)));
    }
}
