<?php

namespace PHPatch\Peg\Definition;

use PHPatch\Peg\AndPredicate;
use PHPatch\Peg\Any;
use PHPatch\Peg\Choice;
use PHPatch\Peg\Literal;
use PHPatch\Peg\Many;
use PHPatch\Peg\Map;
use PHPatch\Peg\Match;
use PHPatch\Peg\NotPredicate;
use PHPatch\Peg\Optional;
use PHPatch\Peg\Sequence;
use PHPatch\Peg\Type;
use PHPatch\Peg\Void;

class Parser
{

    protected function parse_start(Input $input)
    {

        $failed_map_0 = false;

        if (!$failed_map_0) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {

            $result = $this->parse_map($input);


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            } else {
                $map = $result->getValue();

            }
        }

        if (!$failed_map_0) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {
            $result = new Success($map);
        }


        return $result;
    }

    protected function parse__(Input $input)
    {

        if (preg_match('/^\\s*/', $input->restStr(), $matches)) {
            $input->skip(strlen($matches[0]));

            $result = new Success($matches[0]);
        } else {
            $result = new Failure("Expected: /" . '\\s*' . "/ at " . $input->at());
        }


        return $result;
    }

    protected function parse_map(Input $input)
    {

        $copy_choice_0 = $input->copy();

        $failed_map_1 = false;

        if (!$failed_map_1) {

            $result_any_0 = new Success();

            while (true) {
                $copy_any_0 = $input->copy();

                $failed_map_2 = false;

                if (!$failed_map_2) {

                    $copy_choice_1 = $input->copy();

                    $result = $this->parse_labelParser($input);


                    if ($result instanceof Failure) {
                        $input->follow($copy_choice_1);

                        $result = $this->parse_unlabeledParser($input);

                    }


                    if ($result instanceof Failure) {
                        $failed_map_2 = true;

                    } else {
                        $part = $result->getValue();

                    }
                }

                if (!$failed_map_2) {

                    $result = $this->parse__($input);


                    if ($result instanceof Failure) {
                        $failed_map_2 = true;

                    }
                }

                if (!$failed_map_2) {
                    $result = new Success($part);
                }


                if ($result instanceof Failure) {
                    $input->follow($copy_any_0);
                    break;
                }

                $result_any_0 = $result_any_0->concat($result);
            }

            $result = $result_any_0;


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            } else {
                $parts = $result->getValue();

            }
        }

        if (!$failed_map_1) {

            if (substr($input->restStr(), 0, 1) === '{') {
                $input->skip(1);

                $result = new Success('{');
            } else {
                $result = new Failure("Expected: '" . '{' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            $result = $this->parse_expr($input);


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            } else {
                $expr = $result->getValue();

            }
        }

        if (!$failed_map_1) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            if (substr($input->restStr(), 0, 1) === '}') {
                $input->skip(1);

                $result = new Success('}');
            } else {
                $result = new Failure("Expected: '" . '}' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {
            $result = new Success(new Map($parts, trim($expr)));
        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_0);

            $result = $this->parse_sequences($input);

        }


        return $result;
    }

    protected function parse_labelParser(Input $input)
    {

        $failed_map_3 = false;

        if (!$failed_map_3) {

            if (preg_match('/^[\\w_]+/', $input->restStr(), $matches)) {
                $input->skip(strlen($matches[0]));

                $result = new Success($matches[0]);
            } else {
                $result = new Failure("Expected: /" . '[\\w_]+' . "/ at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_3 = true;

            } else {
                $label = $result->getValue();

            }
        }

        if (!$failed_map_3) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_3 = true;

            }
        }

        if (!$failed_map_3) {

            if (substr($input->restStr(), 0, 1) === ':') {
                $input->skip(1);

                $result = new Success(':');
            } else {
                $result = new Failure("Expected: '" . ':' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_3 = true;

            }
        }

        if (!$failed_map_3) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_3 = true;

            }
        }

        if (!$failed_map_3) {

            $result = $this->parse_predicate($input);


            if ($result instanceof Failure) {
                $failed_map_3 = true;

            } else {
                $parser = $result->getValue();

            }
        }

        if (!$failed_map_3) {
            $result = new Success(array($label => $parser));
        }


        return $result;
    }

    protected function parse_unlabeledParser(Input $input)
    {

        $failed_map_4 = false;

        if (!$failed_map_4) {

            $result = $this->parse_predicate($input);


            if ($result instanceof Failure) {
                $failed_map_4 = true;

            } else {
                $parser = $result->getValue();

            }
        }

        if (!$failed_map_4) {
            $result = new Success(array($parser));
        }


        return $result;
    }

    protected function parse_expr(Input $input)
    {

        $result_any_1 = new Success();

        while (true) {
            $copy_any_1 = $input->copy();

            $copy_choice_2 = $input->copy();

            $copy_choice_3 = $input->copy();

            if (preg_match('/^[^{}"]/', $input->restStr(), $matches)) {
                $input->skip(strlen($matches[0]));

                $result = new Success($matches[0]);
            } else {
                $result = new Failure("Expected: /" . '[^{}"]' . "/ at " . $input->at());
            }


            if ($result instanceof Failure) {
                $input->follow($copy_choice_3);

                if (preg_match('/^"(?:[^\\\\"]|\\\\.)*"/', $input->restStr(), $matches)) {
                    $input->skip(strlen($matches[0]));

                    $result = new Success($matches[0]);
                } else {
                    $result = new Failure("Expected: /" . '"(?:[^\\\\"]|\\\\.)*"' . "/ at " . $input->at());
                }

            }


            if ($result instanceof Failure) {
                $input->follow($copy_choice_2);


                if (substr($input->restStr(), 0, 1) === '{') {
                    $input->skip(1);

                    $result = new Success('{');
                } else {
                    $result = new Failure("Expected: '" . '{' . "' at " . $input->at());
                }


                if ($result instanceof Success) {
                    $concat_left_1 = $result;

                    $result = $this->parse_expr($input);


                    if ($result instanceof Success) {
                        $result = $concat_left_1->concat($result);
                    }
                }


                if ($result instanceof Success) {
                    $concat_left_0 = $result;

                    if (substr($input->restStr(), 0, 1) === '}') {
                        $input->skip(1);

                        $result = new Success('}');
                    } else {
                        $result = new Failure("Expected: '" . '}' . "' at " . $input->at());
                    }


                    if ($result instanceof Success) {
                        $result = $concat_left_0->concat($result);
                    }
                }

            }


            if ($result instanceof Failure) {
                $input->follow($copy_any_1);
                break;
            }

            $result_any_1 = $result_any_1->concat($result);
        }

        $result = $result_any_1;


        return $result;
    }

    protected function parse_sequences(Input $input)
    {

        $failed_map_5 = false;

        if (!$failed_map_5) {

            $result = $this->parse_choices($input);


            if ($result instanceof Failure) {
                $failed_map_5 = true;

            } else {
                $first = $result->getValue();

            }
        }

        if (!$failed_map_5) {

            $result_any_2 = new Success();

            while (true) {
                $copy_any_2 = $input->copy();

                $failed_map_6 = false;

                if (!$failed_map_6) {

                    $result = $this->parse__($input);


                    if ($result instanceof Failure) {
                        $failed_map_6 = true;

                    }
                }

                if (!$failed_map_6) {

                    $result = $this->parse_choices($input);


                    if ($result instanceof Failure) {
                        $failed_map_6 = true;

                    } else {
                        $choice = $result->getValue();

                    }
                }

                if (!$failed_map_6) {
                    $result = new Success(array($choice));
                }


                if ($result instanceof Failure) {
                    $input->follow($copy_any_2);
                    break;
                }

                $result_any_2 = $result_any_2->concat($result);
            }

            $result = $result_any_2;


            if ($result instanceof Failure) {
                $failed_map_5 = true;

            } else {
                $rest = $result->getValue();

            }
        }

        if (!$failed_map_5) {
            $result = new Success(array_reduce($rest === null ? array() : $rest, function ($left, $right) {
                return new Sequence($left, $right);
            }, $first));
        }


        return $result;
    }

    protected function parse_choices(Input $input)
    {

        $failed_map_7 = false;

        if (!$failed_map_7) {

            $result = $this->parse_predicate($input);


            if ($result instanceof Failure) {
                $failed_map_7 = true;

            } else {
                $first = $result->getValue();

            }
        }

        if (!$failed_map_7) {

            $result_any_3 = new Success();

            while (true) {
                $copy_any_3 = $input->copy();

                $failed_map_8 = false;

                if (!$failed_map_8) {

                    $result = $this->parse__($input);


                    if ($result instanceof Failure) {
                        $failed_map_8 = true;

                    }
                }

                if (!$failed_map_8) {

                    if (substr($input->restStr(), 0, 1) === '/') {
                        $input->skip(1);

                        $result = new Success('/');
                    } else {
                        $result = new Failure("Expected: '" . '/' . "' at " . $input->at());
                    }


                    if ($result instanceof Failure) {
                        $failed_map_8 = true;

                    }
                }

                if (!$failed_map_8) {

                    $result = $this->parse__($input);


                    if ($result instanceof Failure) {
                        $failed_map_8 = true;

                    }
                }

                if (!$failed_map_8) {

                    $result = $this->parse_predicate($input);


                    if ($result instanceof Failure) {
                        $failed_map_8 = true;

                    } else {
                        $predicate = $result->getValue();

                    }
                }

                if (!$failed_map_8) {
                    $result = new Success(array($predicate));
                }


                if ($result instanceof Failure) {
                    $input->follow($copy_any_3);
                    break;
                }

                $result_any_3 = $result_any_3->concat($result);
            }

            $result = $result_any_3;


            if ($result instanceof Failure) {
                $failed_map_7 = true;

            } else {
                $rest = $result->getValue();

            }
        }

        if (!$failed_map_7) {
            $result = new Success(array_reduce($rest === null ? array() : $rest, function ($left, $right) {
                return new Choice($left, $right);
            }, $first));
        }


        return $result;
    }

    protected function parse_predicate(Input $input)
    {

        $copy_choice_4 = $input->copy();

        $copy_choice_5 = $input->copy();

        $failed_map_9 = false;

        if (!$failed_map_9) {

            if (substr($input->restStr(), 0, 1) === '&') {
                $input->skip(1);

                $result = new Success('&');
            } else {
                $result = new Failure("Expected: '" . '&' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_9 = true;

            }
        }

        if (!$failed_map_9) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_9 = true;

            }
        }

        if (!$failed_map_9) {

            $result = $this->parse_repetition($input);


            if ($result instanceof Failure) {
                $failed_map_9 = true;

            } else {
                $repetition = $result->getValue();

            }
        }

        if (!$failed_map_9) {
            $result = new Success(new AndPredicate($repetition));
        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_5);

            $failed_map_10 = false;

            if (!$failed_map_10) {

                if (substr($input->restStr(), 0, 1) === '!') {
                    $input->skip(1);

                    $result = new Success('!');
                } else {
                    $result = new Failure("Expected: '" . '!' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_10 = true;

                }
            }

            if (!$failed_map_10) {

                $result = $this->parse__($input);


                if ($result instanceof Failure) {
                    $failed_map_10 = true;

                }
            }

            if (!$failed_map_10) {

                $result = $this->parse_repetition($input);


                if ($result instanceof Failure) {
                    $failed_map_10 = true;

                } else {
                    $repetition = $result->getValue();

                }
            }

            if (!$failed_map_10) {
                $result = new Success(new NotPredicate($repetition));
            }

        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_4);

            $result = $this->parse_repetition($input);

        }


        return $result;
    }

    protected function parse_repetition(Input $input)
    {

        $copy_choice_6 = $input->copy();

        $copy_choice_7 = $input->copy();

        $copy_choice_8 = $input->copy();

        $failed_map_11 = false;

        if (!$failed_map_11) {

            $result = $this->parse_terminal($input);


            if ($result instanceof Failure) {
                $failed_map_11 = true;

            } else {
                $terminal = $result->getValue();

            }
        }

        if (!$failed_map_11) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_11 = true;

            }
        }

        if (!$failed_map_11) {

            if (substr($input->restStr(), 0, 1) === '?') {
                $input->skip(1);

                $result = new Success('?');
            } else {
                $result = new Failure("Expected: '" . '?' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_11 = true;

            }
        }

        if (!$failed_map_11) {
            $result = new Success(new Optional($terminal));
        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_8);

            $failed_map_12 = false;

            if (!$failed_map_12) {

                $result = $this->parse_terminal($input);


                if ($result instanceof Failure) {
                    $failed_map_12 = true;

                } else {
                    $terminal = $result->getValue();

                }
            }

            if (!$failed_map_12) {

                $result = $this->parse__($input);


                if ($result instanceof Failure) {
                    $failed_map_12 = true;

                }
            }

            if (!$failed_map_12) {

                if (substr($input->restStr(), 0, 1) === '+') {
                    $input->skip(1);

                    $result = new Success('+');
                } else {
                    $result = new Failure("Expected: '" . '+' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_12 = true;

                }
            }

            if (!$failed_map_12) {
                $result = new Success(new Many($terminal));
            }

        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_7);

            $failed_map_13 = false;

            if (!$failed_map_13) {

                $result = $this->parse_terminal($input);


                if ($result instanceof Failure) {
                    $failed_map_13 = true;

                } else {
                    $terminal = $result->getValue();

                }
            }

            if (!$failed_map_13) {

                $result = $this->parse__($input);


                if ($result instanceof Failure) {
                    $failed_map_13 = true;

                }
            }

            if (!$failed_map_13) {

                if (substr($input->restStr(), 0, 1) === '*') {
                    $input->skip(1);

                    $result = new Success('*');
                } else {
                    $result = new Failure("Expected: '" . '*' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_13 = true;

                }
            }

            if (!$failed_map_13) {
                $result = new Success(new Any($terminal));
            }

        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_6);

            $result = $this->parse_terminal($input);

        }


        return $result;
    }

    protected function parse_terminal(Input $input)
    {

        $copy_choice_9 = $input->copy();

        $copy_choice_10 = $input->copy();

        $copy_choice_11 = $input->copy();

        $copy_choice_12 = $input->copy();

        $failed_map_14 = false;

        if (!$failed_map_14) {

            if (substr($input->restStr(), 0, 1) === '(') {
                $input->skip(1);

                $result = new Success('(');
            } else {
                $result = new Failure("Expected: '" . '(' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_14 = true;

            }
        }

        if (!$failed_map_14) {

            $result = $this->parse__($input);


            if ($result instanceof Failure) {
                $failed_map_14 = true;

            }
        }

        if (!$failed_map_14) {

            if (substr($input->restStr(), 0, 1) === ')') {
                $input->skip(1);

                $result = new Success(')');
            } else {
                $result = new Failure("Expected: '" . ')' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_14 = true;

            }
        }

        if (!$failed_map_14) {
            $result = new Success(new Void());
        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_12);

            $failed_map_15 = false;

            if (!$failed_map_15) {

                if (substr($input->restStr(), 0, 1) === '(') {
                    $input->skip(1);

                    $result = new Success('(');
                } else {
                    $result = new Failure("Expected: '" . '(' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_15 = true;

                }
            }

            if (!$failed_map_15) {

                $result = $this->parse__($input);


                if ($result instanceof Failure) {
                    $failed_map_15 = true;

                }
            }

            if (!$failed_map_15) {

                $result = $this->parse_sequences($input);


                if ($result instanceof Failure) {
                    $failed_map_15 = true;

                } else {
                    $parser = $result->getValue();

                }
            }

            if (!$failed_map_15) {

                $result = $this->parse__($input);


                if ($result instanceof Failure) {
                    $failed_map_15 = true;

                }
            }

            if (!$failed_map_15) {

                if (substr($input->restStr(), 0, 1) === ')') {
                    $input->skip(1);

                    $result = new Success(')');
                } else {
                    $result = new Failure("Expected: '" . ')' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_15 = true;

                }
            }

            if (!$failed_map_15) {
                $result = new Success($parser);
            }

        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_11);

            $failed_map_16 = false;

            if (!$failed_map_16) {

                if (preg_match('/^T_[A-Z]+/', $input->restStr(), $matches)) {
                    $input->skip(strlen($matches[0]));

                    $result = new Success($matches[0]);
                } else {
                    $result = new Failure("Expected: /" . 'T_[A-Z]+' . "/ at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_16 = true;

                } else {
                    $type = $result->getValue();

                }
            }

            if (!$failed_map_16) {
                $result = new Success(new Type(constant($type)));
            }

        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_10);

            $failed_map_17 = false;

            if (!$failed_map_17) {

                if (preg_match('/^"(?:[^\\\\"]|\\\\.)*"/', $input->restStr(), $matches)) {
                    $input->skip(strlen($matches[0]));

                    $result = new Success($matches[0]);
                } else {
                    $result = new Failure("Expected: /" . '"(?:[^\\\\"]|\\\\.)*"' . "/ at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_17 = true;

                } else {
                    $literal = $result->getValue();

                }
            }

            if (!$failed_map_17) {
                $result = new Success(new Literal(eval("return {$literal};")));
            }

        }


        if ($result instanceof Failure) {
            $input->follow($copy_choice_9);

            $failed_map_18 = false;

            if (!$failed_map_18) {

                if (substr($input->restStr(), 0, 1) === '.') {
                    $input->skip(1);

                    $result = new Success('.');
                } else {
                    $result = new Failure("Expected: '" . '.' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $failed_map_18 = true;

                }
            }

            if (!$failed_map_18) {
                $result = new Success(new Match(".*"));
            }

        }


        return $result;
    }

    public function parse(Input $input)
    {
        $output = $this->parse_start($input);

        if ($input->hasNext()) {
            return new Failure("Unexpected '" . $input->next() . "' at " . $input->at());
        }

        return $output;
    }
}
