<?php


namespace PHPeg\Grammar\Tree;


interface NodeInterface
{
    public function accept(VisitorInterface $visitor);
} 
