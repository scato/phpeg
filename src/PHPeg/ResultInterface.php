<?php


namespace PHPeg;


interface ResultInterface
{
    /**
     * @return boolean
     */
    public function isSuccess();

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @return string
     */
    public function getRest();
}
