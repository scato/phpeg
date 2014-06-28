<?php

class ArrayParser
{

    protected function parse_value(Input $input)
    {

        $copy_choice_0 = $input->copy();

        $result = $this->parse_array($input);


        if ($result instanceof Failure) {
            $input->follow($copy_choice_0);

            $result_any_0 = new Success();

            while (true) {
                $copy_any_0 = $input->copy();

                if (preg_match('/^[^\\n]/', $input->restStr(), $matches)) {
                    $input->skip(strlen($matches[0]));

                    $result = new Success($matches[0]);
                } else {
                    $result = new Failure("Expected: /" . '[^\\n]' . "/ at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $input->follow($copy_any_0);
                    break;
                }

                $result_any_0 = $result_any_0->concat($result);
            }

            $result = $result_any_0;

        }


        return $result;
    }

    protected function parse_keyValue(Input $input)
    {

        $failed_map_0 = false;

        if (!$failed_map_0) {

            $result_any_1 = new Success();

            while (true) {
                $copy_any_1 = $input->copy();

                if (substr($input->restStr(), 0, 4) === '    ') {
                    $input->skip(4);

                    $result = new Success('    ');
                } else {
                    $result = new Failure("Expected: '" . '    ' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $input->follow($copy_any_1);
                    break;
                }

                $result_any_1 = $result_any_1->concat($result);
            }

            $result = $result_any_1;


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {

            if (substr($input->restStr(), 0, 1) === '[') {
                $input->skip(1);

                $result = new Success('[');
            } else {
                $result = new Failure("Expected: '" . '[' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {

            if (preg_match('/^[^\\]]*/', $input->restStr(), $matches)) {
                $input->skip(strlen($matches[0]));

                $result = new Success($matches[0]);
            } else {
                $result = new Failure("Expected: /" . '[^\\]]*' . "/ at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            } else {
                $key = $result->getValue();

            }
        }

        if (!$failed_map_0) {

            if (substr($input->restStr(), 0, 1) === ']') {
                $input->skip(1);

                $result = new Success(']');
            } else {
                $result = new Failure("Expected: '" . ']' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {

            if (substr($input->restStr(), 0, 1) === ' ') {
                $input->skip(1);

                $result = new Success(' ');
            } else {
                $result = new Failure("Expected: '" . ' ' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {

            if (substr($input->restStr(), 0, 2) === '=>') {
                $input->skip(2);

                $result = new Success('=>');
            } else {
                $result = new Failure("Expected: '" . '=>' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {

            if (substr($input->restStr(), 0, 1) === ' ') {
                $input->skip(1);

                $result = new Success(' ');
            } else {
                $result = new Failure("Expected: '" . ' ' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {

            $result = $this->parse_value($input);


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            } else {
                $value = $result->getValue();

            }
        }

        if (!$failed_map_0) {

            if (preg_match('/^\\n/', $input->restStr(), $matches)) {
                $input->skip(strlen($matches[0]));

                $result = new Success($matches[0]);
            } else {
                $result = new Failure("Expected: /" . '\\n' . "/ at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_0 = true;

            }
        }

        if (!$failed_map_0) {
            $result = new Success(
                array(array($key => $value))
            );
        }


        return $result;
    }

    protected function parse_array(Input $input)
    {

        $failed_map_1 = false;

        if (!$failed_map_1) {

            if (substr($input->restStr(), 0, 5) === 'Array') {
                $input->skip(5);

                $result = new Success('Array');
            } else {
                $result = new Failure("Expected: '" . 'Array' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            if (preg_match('/^\\n/', $input->restStr(), $matches)) {
                $input->skip(strlen($matches[0]));

                $result = new Success($matches[0]);
            } else {
                $result = new Failure("Expected: /" . '\\n' . "/ at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            $result_any_2 = new Success();

            while (true) {
                $copy_any_2 = $input->copy();

                if (substr($input->restStr(), 0, 4) === '    ') {
                    $input->skip(4);

                    $result = new Success('    ');
                } else {
                    $result = new Failure("Expected: '" . '    ' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $input->follow($copy_any_2);
                    break;
                }

                $result_any_2 = $result_any_2->concat($result);
            }

            $result = $result_any_2;


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            if (substr($input->restStr(), 0, 1) === '(') {
                $input->skip(1);

                $result = new Success('(');
            } else {
                $result = new Failure("Expected: '" . '(' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            if (preg_match('/^\\n/', $input->restStr(), $matches)) {
                $input->skip(strlen($matches[0]));

                $result = new Success($matches[0]);
            } else {
                $result = new Failure("Expected: /" . '\\n' . "/ at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            $result_any_3 = new Success();

            while (true) {
                $copy_any_3 = $input->copy();

                $result = $this->parse_keyValue($input);


                if ($result instanceof Failure) {
                    $input->follow($copy_any_3);
                    break;
                }

                $result_any_3 = $result_any_3->concat($result);
            }

            $result = $result_any_3;


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            } else {
                $elements = $result->getValue();

            }
        }

        if (!$failed_map_1) {

            $result_any_4 = new Success();

            while (true) {
                $copy_any_4 = $input->copy();

                if (substr($input->restStr(), 0, 4) === '    ') {
                    $input->skip(4);

                    $result = new Success('    ');
                } else {
                    $result = new Failure("Expected: '" . '    ' . "' at " . $input->at());
                }


                if ($result instanceof Failure) {
                    $input->follow($copy_any_4);
                    break;
                }

                $result_any_4 = $result_any_4->concat($result);
            }

            $result = $result_any_4;


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            if (substr($input->restStr(), 0, 1) === ')') {
                $input->skip(1);

                $result = new Success(')');
            } else {
                $result = new Failure("Expected: '" . ')' . "' at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {

            if (preg_match('/^\\n/', $input->restStr(), $matches)) {
                $input->skip(strlen($matches[0]));

                $result = new Success($matches[0]);
            } else {
                $result = new Failure("Expected: /" . '\\n' . "/ at " . $input->at());
            }


            if ($result instanceof Failure) {
                $failed_map_1 = true;

            }
        }

        if (!$failed_map_1) {
            $result = new Success(
                $elements === null ? array() : array_reduce($elements, 'array_merge', array())
            );
        }


        return $result;
    }

    public function parse(Input $input)
    {
        return $this->parse_array($input);
    }
}
