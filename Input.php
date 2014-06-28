<?php

class Input
{
    public function __construct($str)
    {
        $this->str = $str;
        $this->pos = 0;
    }

    public function next()
    {
        return substr($this->str, $this->pos++, 1);
    }

    public function hasNext()
    {
        return $this->pos < strlen($this->str);
    }

    public function copy()
    {
        $copy = new Input($this->str);
        $copy->follow($this);

        return $copy;
    }

    public function follow(Input $input)
    {
        $this->pos = $input->pos;
    }

    public function skip($len)
    {
        $this->pos += $len;
    }

    public function at()
    {
        $lines = explode("\n", substr($this->str, 0, $this->pos));

        $line = count($lines);
        $char = strlen(array_pop($lines)) + 1;

        return "line {$line}, char {$char}";
    }

    public function restStr()
    {
        return substr($this->str, $this->pos);
    }
}
