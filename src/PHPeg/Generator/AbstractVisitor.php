<?php


namespace PHPeg\Generator;

use PHPeg\Grammar\Tree\VisitorInterface;

abstract class AbstractVisitor implements VisitorInterface
{
    protected $results = array();

    public function getResult()
    {
        return array_pop($this->results);
    }

    protected function getResults($count)
    {
        $results = array();

        for ($i = 0; $i < $count; $i++) {
            array_unshift($results, array_pop($this->results));
        }

        return $results;
    }
}
