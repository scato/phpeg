<?php

namespace PHPeg\Combinator;

use PHPeg\ContextInterface;
use PHPeg\ExpressionInterface;
use PHPeg\ResultInterface;

class OneOrMore implements ExpressionInterface
{
    private $first;
    private $rest;

    public function __construct(ExpressionInterface $expression)
    {
        // expression+ = first:expression rest:expression* { return array_merge(array($first), $rest); };
        $this->first = $expression;
        $this->rest = new ZeroOrMore($expression);
        /*
        parent::__construct(new Action(
            new Sequence(new Label('first', $expression), new Label('rest', new ZeroOrMore($expression))),
            'return array_merge(array($first), $rest);'
        ));
        */
    }

    /**
     * @param string $string
     * @param ContextInterface $context
     * @return ResultInterface
     * @throws \LogicException
     */
    public function parse($string, ContextInterface $context)
    {
        $first = $this->first->parse($string, $context);

        if (!$first->isSuccess()) {
            return $first;
        }

        $rest = $this->rest->parse($first->getRest(), $context);

        if (!$rest->isSuccess()) {
            throw new \LogicException("ZeroOrMore should never result in failure");
        }

        $result = array_merge(array($first->getResult()), $rest->getResult());

        return new Success($result, $rest->getRest());
    }
}
