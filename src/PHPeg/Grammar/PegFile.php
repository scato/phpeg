<?php

namespace PHPeg\Grammar;

use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\AndPredicateNode;
use PHPeg\Grammar\Tree\AnyNode;
use PHPeg\Grammar\Tree\CharacterClassNode;
use PHPeg\Grammar\Tree\ChoiceNode;
use PHPeg\Grammar\Tree\GrammarNode;
use PHPeg\Grammar\Tree\LabelNode;
use PHPeg\Grammar\Tree\LiteralNode;
use PHPeg\Grammar\Tree\MatchedStringNode;
use PHPeg\Grammar\Tree\NotPredicateNode;
use PHPeg\Grammar\Tree\OneOrMoreNode;
use PHPeg\Grammar\Tree\OptionalNode;
use PHPeg\Grammar\Tree\RuleNode;
use PHPeg\Grammar\Tree\RuleReferenceNode;
use PHPeg\Grammar\Tree\SequenceNode;
use PHPeg\Grammar\Tree\ZeroOrMoreNode;

class PegFile
{
    protected $string;
    protected $position;
    protected $positions = array();
    protected $value;
    protected $values = array();
    protected $cache;
    protected $expecting = array();

    protected function parsePegFile()
    {
        $_position = $this->position;

        if (isset($this->cache['PegFile'][$_position])) {
            $_success = $this->cache['PegFile'][$_position]['success'];
            $this->position = $this->cache['PegFile'][$_position]['position'];
            $this->value = $this->cache['PegFile'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        $this->positions[] = $this->position;

        $this->values[] = array();

        $_success = $this->parse_();

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseNamespace();

            if ($_success) {
                $namespace = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if (!$_success) {
            $_success = true;
            $this->position = end($this->positions);
            $this->value = null;
        }

        array_pop($this->positions);

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->values[] = array();

                $_success = $this->parse_();

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $_success = $this->parseImport();

                    if ($_success) {
                        $import = $this->value;
                    }
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $this->value = array_pop($this->values);
                } else {
                    array_pop($this->values);
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$namespace, &$import) {
                        return $import;
                    });
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);

            if ($_success) {
                $imports = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseGrammar();

            if ($_success) {
                $grammar = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$namespace, &$import, &$imports, &$grammar) {
                if (isset($namespace)) $grammar->setNamespace($namespace);
                $grammar->setImports($imports);
                return $grammar;
            });
        }

        $this->cache['PegFile'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'PegFile';
        }

        return $_success;
    }

    protected function parseQualifiedIdentifier()
    {
        $_position = $this->position;

        if (isset($this->cache['QualifiedIdentifier'][$_position])) {
            $_success = $this->cache['QualifiedIdentifier'][$_position]['success'];
            $this->position = $this->cache['QualifiedIdentifier'][$_position]['position'];
            $this->value = $this->cache['QualifiedIdentifier'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;
        $this->values[] = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->values[] = array();

                if (substr($this->string, $this->position, 1) === '\\') {
                    $_success = true;
                    $this->value = '\\';
                    $this->position += 1;
                } else {
                    $_success = false;
                    $this->expecting[$this->position][] = '\\';
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $_success = $this->parseIdentifier();
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $this->value = array_pop($this->values);
                } else {
                    array_pop($this->values);
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = strval(substr($this->string, end($this->positions), $this->position - end($this->positions)));
        }

        array_pop($this->positions);

        $this->cache['QualifiedIdentifier'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'QualifiedIdentifier';
        }

        return $_success;
    }

    protected function parseNamespace()
    {
        $_position = $this->position;

        if (isset($this->cache['Namespace'][$_position])) {
            $_success = $this->cache['Namespace'][$_position]['success'];
            $this->position = $this->cache['Namespace'][$_position]['position'];
            $this->value = $this->cache['Namespace'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 9) === 'namespace') {
            $_success = true;
            $this->value = 'namespace';
            $this->position += 9;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = 'namespace';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseQualifiedIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === ';') {
                $_success = true;
                $this->value = ';';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = ';';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name) {
                return $name;
            });
        }

        $this->cache['Namespace'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Namespace';
        }

        return $_success;
    }

    protected function parseImport()
    {
        $_position = $this->position;

        if (isset($this->cache['Import'][$_position])) {
            $_success = $this->cache['Import'][$_position]['success'];
            $this->position = $this->cache['Import'][$_position]['position'];
            $this->value = $this->cache['Import'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 3) === 'use') {
            $_success = true;
            $this->value = 'use';
            $this->position += 3;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = 'use';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseQualifiedIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === ';') {
                $_success = true;
                $this->value = ';';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = ';';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name) {
                return $name;
            });
        }

        $this->cache['Import'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Import';
        }

        return $_success;
    }

    protected function parseGrammar()
    {
        $_position = $this->position;

        if (isset($this->cache['Grammar'][$_position])) {
            $_success = $this->cache['Grammar'][$_position]['success'];
            $this->position = $this->cache['Grammar'][$_position]['position'];
            $this->value = $this->cache['Grammar'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 7) === 'grammar') {
            $_success = true;
            $this->value = 'grammar';
            $this->position += 7;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = 'grammar';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->positions[] = $this->position;

            $this->values[] = array();

            $_success = $this->parse_();

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                if (substr($this->string, $this->position, 7) === 'extends') {
                    $_success = true;
                    $this->value = 'extends';
                    $this->position += 7;
                } else {
                    $_success = false;
                    $this->expecting[$this->position][] = 'extends';
                }
            }

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                $_success = $this->parse_();
            }

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                $_success = $this->parseIdentifier();

                if ($_success) {
                    $base = $this->value;
                }
            }

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                $this->value = array_pop($this->values);
            } else {
                array_pop($this->values);
            }

            if (!$_success) {
                $_success = true;
                $this->position = end($this->positions);
                $this->value = null;
            }

            array_pop($this->positions);
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '{') {
                $_success = true;
                $this->value = '{';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '{';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->positions[] = $this->position;

            $this->values[] = array();

            $_success = $this->parse_();

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                if (substr($this->string, $this->position, 5) === 'start') {
                    $_success = true;
                    $this->value = 'start';
                    $this->position += 5;
                } else {
                    $_success = false;
                    $this->expecting[$this->position][] = 'start';
                }
            }

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                $_success = $this->parse_();
            }

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                $_success = $this->parseRule();

                if ($_success) {
                    $start = $this->value;
                }
            }

            if ($_success) {
                $this->values[] = array_merge(array_pop($this->values), array($this->value));

                $this->value = array_pop($this->values);
            } else {
                array_pop($this->values);
            }

            if (!$_success) {
                $_success = true;
                $this->position = end($this->positions);
                $this->value = null;
            }

            array_pop($this->positions);
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->values[] = array();

                $_success = $this->parse_();

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $_success = $this->parseRule();

                    if ($_success) {
                        $rule = $this->value;
                    }
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $this->value = array_pop($this->values);
                } else {
                    array_pop($this->values);
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$name, &$base, &$start, &$rule) {
                        return $rule;
                    });
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);

            if ($_success) {
                $rules = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '}') {
                $_success = true;
                $this->value = '}';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '}';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name, &$base, &$start, &$rule, &$rules) {
                $grammar = new GrammarNode($name, isset($start) ? $start->getName() : null, array_merge(array($start), $rules));
                    if (isset($base)) $grammar->setBase($base);
                    return $grammar;
            });
        }

        $this->cache['Grammar'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Grammar';
        }

        return $_success;
    }

    protected function parseRule()
    {
        $_position = $this->position;

        if (isset($this->cache['Rule'][$_position])) {
            $_success = $this->cache['Rule'][$_position]['success'];
            $this->position = $this->cache['Rule'][$_position]['position'];
            $this->value = $this->cache['Rule'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $name = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '=') {
                $_success = true;
                $this->value = '=';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '=';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseExpression();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === ';') {
                $_success = true;
                $this->value = ';';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = ';';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name, &$expression) {
                return new RuleNode($name, $expression);
            });
        }

        $this->cache['Rule'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Rule';
        }

        return $_success;
    }

    protected function parseLabel()
    {
        $_position = $this->position;

        if (isset($this->cache['Label'][$_position])) {
            $_success = $this->cache['Label'][$_position]['success'];
            $this->position = $this->cache['Label'][$_position]['position'];
            $this->value = $this->cache['Label'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;

        $this->values[] = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $name = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === ':') {
                $_success = true;
                $this->value = ':';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = ':';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parsePredicate();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name, &$expression) {
                return new LabelNode($name, $expression);
            });
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parsePredicate();
        }

        array_pop($this->positions);

        $this->cache['Label'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Label';
        }

        return $_success;
    }

    protected function parseSequence()
    {
        $_position = $this->position;

        if (isset($this->cache['Sequence'][$_position])) {
            $_success = $this->cache['Sequence'][$_position]['success'];
            $this->position = $this->cache['Sequence'][$_position]['position'];
            $this->value = $this->cache['Sequence'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        $_success = $this->parseLabel();

        if ($_success) {
            $first = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->values[] = array();

                $_success = $this->parse_();

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $_success = $this->parseLabel();

                    if ($_success) {
                        $next = $this->value;
                    }
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $this->value = array_pop($this->values);
                } else {
                    array_pop($this->values);
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$first, &$next) {
                        return $next;
                    });
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);

            if ($_success) {
                $rest = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$first, &$next, &$rest) {
                return empty($rest) ? $first : new SequenceNode(array_merge(array($first), $rest));
            });
        }

        $this->cache['Sequence'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Sequence';
        }

        return $_success;
    }

    protected function parseCode()
    {
        $_position = $this->position;

        if (isset($this->cache['Code'][$_position])) {
            $_success = $this->cache['Code'][$_position]['success'];
            $this->position = $this->cache['Code'][$_position]['position'];
            $this->value = $this->cache['Code'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;
        $this->values[] = array();

        while (true) {
            $this->positions[] = $this->position;
            $this->positions[] = $this->position;

            if (preg_match('/^[^{}]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }

            if (!$_success) {
                $this->position = end($this->positions);
                $this->values[] = array();

                if (substr($this->string, $this->position, 1) === '{') {
                    $_success = true;
                    $this->value = '{';
                    $this->position += 1;
                } else {
                    $_success = false;
                    $this->expecting[$this->position][] = '{';
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $_success = $this->parseCode();
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    if (substr($this->string, $this->position, 1) === '}') {
                        $_success = true;
                        $this->value = '}';
                        $this->position += 1;
                    } else {
                        $_success = false;
                        $this->expecting[$this->position][] = '}';
                    }
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $this->value = array_pop($this->values);
                } else {
                    array_pop($this->values);
                }
            }

            array_pop($this->positions);

            if (!$_success) {
                $this->position = array_pop($this->positions);

                break;
            }

            array_pop($this->positions);
            $this->values[] = array_merge(array_pop($this->values), array($this->value));
        }

        $_success = true;
        $this->value = array_pop($this->values);

        if ($_success) {
            $this->value = strval(substr($this->string, end($this->positions), $this->position - end($this->positions)));
        }

        array_pop($this->positions);

        $this->cache['Code'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Code';
        }

        return $_success;
    }

    protected function parseAction()
    {
        $_position = $this->position;

        if (isset($this->cache['Action'][$_position])) {
            $_success = $this->cache['Action'][$_position]['success'];
            $this->position = $this->cache['Action'][$_position]['position'];
            $this->value = $this->cache['Action'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;

        $this->values[] = array();

        $_success = $this->parseSequence();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '{') {
                $_success = true;
                $this->value = '{';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '{';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseCode();

            if ($_success) {
                $code = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '}') {
                $_success = true;
                $this->value = '}';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '}';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression, &$code) {
                return new ActionNode($expression, trim($code));
            });
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseSequence();
        }

        array_pop($this->positions);

        $this->cache['Action'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Action';
        }

        return $_success;
    }

    protected function parseChoice()
    {
        $_position = $this->position;

        if (isset($this->cache['Choice'][$_position])) {
            $_success = $this->cache['Choice'][$_position]['success'];
            $this->position = $this->cache['Choice'][$_position]['position'];
            $this->value = $this->cache['Choice'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        $_success = $this->parseAction();

        if ($_success) {
            $first = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->values[] = array();

                $_success = $this->parse_();

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    if (substr($this->string, $this->position, 1) === '/') {
                        $_success = true;
                        $this->value = '/';
                        $this->position += 1;
                    } else {
                        $_success = false;
                        $this->expecting[$this->position][] = '/';
                    }
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $_success = $this->parse_();
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $_success = $this->parseAction();

                    if ($_success) {
                        $next = $this->value;
                    }
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $this->value = array_pop($this->values);
                } else {
                    array_pop($this->values);
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$first, &$next) {
                        return $next;
                    });
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);

            if ($_success) {
                $rest = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$first, &$next, &$rest) {
                return empty($rest) ? $first : new ChoiceNode(array_merge(array($first), $rest));
            });
        }

        $this->cache['Choice'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Choice';
        }

        return $_success;
    }

    protected function parseExpression()
    {
        $_position = $this->position;

        if (isset($this->cache['Expression'][$_position])) {
            $_success = $this->cache['Expression'][$_position]['success'];
            $this->position = $this->cache['Expression'][$_position]['position'];
            $this->value = $this->cache['Expression'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseChoice();

        $this->cache['Expression'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Expression';
        }

        return $_success;
    }

    protected function parseZeroOrMore()
    {
        $_position = $this->position;

        if (isset($this->cache['ZeroOrMore'][$_position])) {
            $_success = $this->cache['ZeroOrMore'][$_position]['success'];
            $this->position = $this->cache['ZeroOrMore'][$_position]['position'];
            $this->value = $this->cache['ZeroOrMore'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '*') {
                $_success = true;
                $this->value = '*';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '*';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression) {
                return new ZeroOrMoreNode($expression);
            });
        }

        $this->cache['ZeroOrMore'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'ZeroOrMore';
        }

        return $_success;
    }

    protected function parseOneOrMore()
    {
        $_position = $this->position;

        if (isset($this->cache['OneOrMore'][$_position])) {
            $_success = $this->cache['OneOrMore'][$_position]['success'];
            $this->position = $this->cache['OneOrMore'][$_position]['position'];
            $this->value = $this->cache['OneOrMore'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '+') {
                $_success = true;
                $this->value = '+';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '+';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression) {
                return new OneOrMoreNode($expression);
            });
        }

        $this->cache['OneOrMore'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'OneOrMore';
        }

        return $_success;
    }

    protected function parseOptional()
    {
        $_position = $this->position;

        if (isset($this->cache['Optional'][$_position])) {
            $_success = $this->cache['Optional'][$_position]['success'];
            $this->position = $this->cache['Optional'][$_position]['position'];
            $this->value = $this->cache['Optional'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '?') {
                $_success = true;
                $this->value = '?';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '?';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression) {
                return new OptionalNode($expression);
            });
        }

        $this->cache['Optional'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Optional';
        }

        return $_success;
    }

    protected function parseRepetition()
    {
        $_position = $this->position;

        if (isset($this->cache['Repetition'][$_position])) {
            $_success = $this->cache['Repetition'][$_position]['success'];
            $this->position = $this->cache['Repetition'][$_position]['position'];
            $this->value = $this->cache['Repetition'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;

        $_success = $this->parseZeroOrMore();

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseOneOrMore();
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseOptional();
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseTerminal();
        }

        array_pop($this->positions);

        $this->cache['Repetition'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Repetition';
        }

        return $_success;
    }

    protected function parseAndPredicate()
    {
        $_position = $this->position;

        if (isset($this->cache['AndPredicate'][$_position])) {
            $_success = $this->cache['AndPredicate'][$_position]['success'];
            $this->position = $this->cache['AndPredicate'][$_position]['position'];
            $this->value = $this->cache['AndPredicate'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 1) === '&') {
            $_success = true;
            $this->value = '&';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '&';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression) {
                return new AndPredicateNode($expression);
            });
        }

        $this->cache['AndPredicate'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'AndPredicate';
        }

        return $_success;
    }

    protected function parseNotPredicate()
    {
        $_position = $this->position;

        if (isset($this->cache['NotPredicate'][$_position])) {
            $_success = $this->cache['NotPredicate'][$_position]['success'];
            $this->position = $this->cache['NotPredicate'][$_position]['position'];
            $this->value = $this->cache['NotPredicate'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 1) === '!') {
            $_success = true;
            $this->value = '!';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '!';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression) {
                return new NotPredicateNode($expression);
            });
        }

        $this->cache['NotPredicate'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'NotPredicate';
        }

        return $_success;
    }

    protected function parseMatchedString()
    {
        $_position = $this->position;

        if (isset($this->cache['MatchedString'][$_position])) {
            $_success = $this->cache['MatchedString'][$_position]['success'];
            $this->position = $this->cache['MatchedString'][$_position]['position'];
            $this->value = $this->cache['MatchedString'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 1) === '$') {
            $_success = true;
            $this->value = '$';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '$';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression) {
                return new MatchedStringNode($expression);
            });
        }

        $this->cache['MatchedString'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'MatchedString';
        }

        return $_success;
    }

    protected function parsePredicate()
    {
        $_position = $this->position;

        if (isset($this->cache['Predicate'][$_position])) {
            $_success = $this->cache['Predicate'][$_position]['success'];
            $this->position = $this->cache['Predicate'][$_position]['position'];
            $this->value = $this->cache['Predicate'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;

        $_success = $this->parseAndPredicate();

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseNotPredicate();
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseMatchedString();
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseRepetition();
        }

        array_pop($this->positions);

        $this->cache['Predicate'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Predicate';
        }

        return $_success;
    }

    protected function parseLiteral()
    {
        $_position = $this->position;

        if (isset($this->cache['Literal'][$_position])) {
            $_success = $this->cache['Literal'][$_position]['success'];
            $this->position = $this->cache['Literal'][$_position]['position'];
            $this->value = $this->cache['Literal'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 1) === '"') {
            $_success = true;
            $this->value = '"';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '"';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->positions[] = $this->position;
            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->positions[] = $this->position;

                if (preg_match('/^[^\\\\"]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = end($this->positions);
                    $this->values[] = array();

                    if (substr($this->string, $this->position, 1) === '\\') {
                        $_success = true;
                        $this->value = '\\';
                        $this->position += 1;
                    } else {
                        $_success = false;
                        $this->expecting[$this->position][] = '\\';
                    }

                    if ($_success) {
                        $this->values[] = array_merge(array_pop($this->values), array($this->value));

                        if ($this->position < strlen($this->string)) {
                            $_success = true;
                            $this->value = substr($this->string, $this->position, 1);
                            $this->position += 1;
                        } else {
                            $_success = false;
                        }
                    }

                    if ($_success) {
                        $this->values[] = array_merge(array_pop($this->values), array($this->value));

                        $this->value = array_pop($this->values);
                    } else {
                        array_pop($this->values);
                    }
                }

                array_pop($this->positions);

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);

            if ($_success) {
                $this->value = strval(substr($this->string, end($this->positions), $this->position - end($this->positions)));
            }

            array_pop($this->positions);

            if ($_success) {
                $string = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === '"') {
                $_success = true;
                $this->value = '"';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '"';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$string) {
                return new LiteralNode(stripslashes($string));
            });
        }

        $this->cache['Literal'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Literal';
        }

        return $_success;
    }

    protected function parseAny()
    {
        $_position = $this->position;

        if (isset($this->cache['Any'][$_position])) {
            $_success = $this->cache['Any'][$_position]['success'];
            $this->position = $this->cache['Any'][$_position]['position'];
            $this->value = $this->cache['Any'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, 1) === '.') {
            $_success = true;
            $this->value = '.';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '.';
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return new AnyNode();
            });
        }

        $this->cache['Any'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Any';
        }

        return $_success;
    }

    protected function parseCharacterClass()
    {
        $_position = $this->position;

        if (isset($this->cache['CharacterClass'][$_position])) {
            $_success = $this->cache['CharacterClass'][$_position]['success'];
            $this->position = $this->cache['CharacterClass'][$_position]['position'];
            $this->value = $this->cache['CharacterClass'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 1) === '[') {
            $_success = true;
            $this->value = '[';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '[';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->positions[] = $this->position;
            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->positions[] = $this->position;

                if (preg_match('/^[^\\\\\\]]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = end($this->positions);
                    $this->values[] = array();

                    if (substr($this->string, $this->position, 1) === '\\') {
                        $_success = true;
                        $this->value = '\\';
                        $this->position += 1;
                    } else {
                        $_success = false;
                        $this->expecting[$this->position][] = '\\';
                    }

                    if ($_success) {
                        $this->values[] = array_merge(array_pop($this->values), array($this->value));

                        if ($this->position < strlen($this->string)) {
                            $_success = true;
                            $this->value = substr($this->string, $this->position, 1);
                            $this->position += 1;
                        } else {
                            $_success = false;
                        }
                    }

                    if ($_success) {
                        $this->values[] = array_merge(array_pop($this->values), array($this->value));

                        $this->value = array_pop($this->values);
                    } else {
                        array_pop($this->values);
                    }
                }

                array_pop($this->positions);

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);

            if ($_success) {
                $this->value = strval(substr($this->string, end($this->positions), $this->position - end($this->positions)));
            }

            array_pop($this->positions);

            if ($_success) {
                $string = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === ']') {
                $_success = true;
                $this->value = ']';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = ']';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$string) {
                return new CharacterClassNode($string);
            });
        }

        $this->cache['CharacterClass'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'CharacterClass';
        }

        return $_success;
    }

    protected function parseIdentifier()
    {
        $_position = $this->position;

        if (isset($this->cache['Identifier'][$_position])) {
            $_success = $this->cache['Identifier'][$_position]['success'];
            $this->position = $this->cache['Identifier'][$_position]['position'];
            $this->value = $this->cache['Identifier'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;
        $this->values[] = array();

        if (preg_match('/^[A-Za-z_]$/', substr($this->string, $this->position, 1))) {
            $_success = true;
            $this->value = substr($this->string, $this->position, 1);
            $this->position += 1;
        } else {
            $_success = false;
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                if (preg_match('/^[A-Za-z0-9_]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = strval(substr($this->string, end($this->positions), $this->position - end($this->positions)));
        }

        array_pop($this->positions);

        $this->cache['Identifier'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Identifier';
        }

        return $_success;
    }

    protected function parseRuleReference()
    {
        $_position = $this->position;

        if (isset($this->cache['RuleReference'][$_position])) {
            $_success = $this->cache['RuleReference'][$_position]['success'];
            $this->position = $this->cache['RuleReference'][$_position]['position'];
            $this->value = $this->cache['RuleReference'][$_position]['value'];

            return $_success;
        }

        $_success = $this->parseIdentifier();

        if ($_success) {
            $name = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name) {
                return new RuleReferenceNode($name);
            });
        }

        $this->cache['RuleReference'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'RuleReference';
        }

        return $_success;
    }

    protected function parseSubExpression()
    {
        $_position = $this->position;

        if (isset($this->cache['SubExpression'][$_position])) {
            $_success = $this->cache['SubExpression'][$_position]['success'];
            $this->position = $this->cache['SubExpression'][$_position]['position'];
            $this->value = $this->cache['SubExpression'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 1) === '(') {
            $_success = true;
            $this->value = '(';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '(';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parseExpression();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $_success = $this->parse_();
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 1) === ')') {
                $_success = true;
                $this->value = ')';
                $this->position += 1;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = ')';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression) {
                return $expression;
            });
        }

        $this->cache['SubExpression'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'SubExpression';
        }

        return $_success;
    }

    protected function parseTerminal()
    {
        $_position = $this->position;

        if (isset($this->cache['Terminal'][$_position])) {
            $_success = $this->cache['Terminal'][$_position]['success'];
            $this->position = $this->cache['Terminal'][$_position]['position'];
            $this->value = $this->cache['Terminal'][$_position]['value'];

            return $_success;
        }

        $this->positions[] = $this->position;

        $_success = $this->parseRuleReference();

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseLiteral();
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseAny();
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseCharacterClass();
        }

        if (!$_success) {
            $this->position = end($this->positions);
            $_success = $this->parseSubExpression();
        }

        array_pop($this->positions);

        $this->cache['Terminal'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Terminal';
        }

        return $_success;
    }

    protected function parse_()
    {
        $_position = $this->position;

        if (isset($this->cache['_'][$_position])) {
            $_success = $this->cache['_'][$_position]['success'];
            $this->position = $this->cache['_'][$_position]['position'];
            $this->value = $this->cache['_'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        while (true) {
            $this->positions[] = $this->position;
            $this->positions[] = $this->position;

            $_success = $this->parseWhitespace();

            if (!$_success) {
                $this->position = end($this->positions);
                $_success = $this->parseBlockComment();
            }

            if (!$_success) {
                $this->position = end($this->positions);
                $_success = $this->parseInlineComment();
            }

            array_pop($this->positions);

            if (!$_success) {
                $this->position = array_pop($this->positions);

                break;
            }

            array_pop($this->positions);
            $this->values[] = array_merge(array_pop($this->values), array($this->value));
        }

        $_success = true;
        $this->value = array_pop($this->values);

        if ($_success) {
            $this->value = call_user_func(function () {
                return null;
            });
        }

        $this->cache['_'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = '_';
        }

        return $_success;
    }

    protected function parseWhitespace()
    {
        $_position = $this->position;

        if (isset($this->cache['Whitespace'][$_position])) {
            $_success = $this->cache['Whitespace'][$_position]['success'];
            $this->position = $this->cache['Whitespace'][$_position]['position'];
            $this->value = $this->cache['Whitespace'][$_position]['value'];

            return $_success;
        }

        if (preg_match('/^[ \\n\\r\\t]$/', substr($this->string, $this->position, 1))) {
            $_success = true;
            $this->value = substr($this->string, $this->position, 1);
            $this->position += 1;
        } else {
            $_success = false;
        }

        $this->cache['Whitespace'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'Whitespace';
        }

        return $_success;
    }

    protected function parseBlockComment()
    {
        $_position = $this->position;

        if (isset($this->cache['BlockComment'][$_position])) {
            $_success = $this->cache['BlockComment'][$_position]['success'];
            $this->position = $this->cache['BlockComment'][$_position]['position'];
            $this->value = $this->cache['BlockComment'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 2) === '/*') {
            $_success = true;
            $this->value = '/*';
            $this->position += 2;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '/*';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                $this->values[] = array();

                $this->positions[] = $this->position;

                if (substr($this->string, $this->position, 2) === '*/') {
                    $_success = true;
                    $this->value = '*/';
                    $this->position += 2;
                } else {
                    $_success = false;
                    $this->expecting[$this->position][] = '*/';
                }

                if (!$_success) {
                    $_success = true;
                    $this->value = null;
                } else {
                    $_success = false;
                }

                $this->position = array_pop($this->positions);

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    if ($this->position < strlen($this->string)) {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, 1);
                        $this->position += 1;
                    } else {
                        $_success = false;
                    }
                }

                if ($_success) {
                    $this->values[] = array_merge(array_pop($this->values), array($this->value));

                    $this->value = array_pop($this->values);
                } else {
                    array_pop($this->values);
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            if (substr($this->string, $this->position, 2) === '*/') {
                $_success = true;
                $this->value = '*/';
                $this->position += 2;
            } else {
                $_success = false;
                $this->expecting[$this->position][] = '*/';
            }
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        $this->cache['BlockComment'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'BlockComment';
        }

        return $_success;
    }

    protected function parseInlineComment()
    {
        $_position = $this->position;

        if (isset($this->cache['InlineComment'][$_position])) {
            $_success = $this->cache['InlineComment'][$_position]['success'];
            $this->position = $this->cache['InlineComment'][$_position]['position'];
            $this->value = $this->cache['InlineComment'][$_position]['value'];

            return $_success;
        }

        $this->values[] = array();

        if (substr($this->string, $this->position, 2) === '//') {
            $_success = true;
            $this->value = '//';
            $this->position += 2;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '//';
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->values[] = array();

            while (true) {
                $this->positions[] = $this->position;
                if (preg_match('/^[^\\r\\n]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = array_pop($this->positions);

                    break;
                }

                array_pop($this->positions);
                $this->values[] = array_merge(array_pop($this->values), array($this->value));
            }

            $_success = true;
            $this->value = array_pop($this->values);
        }

        if ($_success) {
            $this->values[] = array_merge(array_pop($this->values), array($this->value));

            $this->value = array_pop($this->values);
        } else {
            array_pop($this->values);
        }

        $this->cache['InlineComment'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->expecting[$_position][] = 'InlineComment';
        }

        return $_success;
    }

    private function line()
    {
        return count(explode("\n", substr($this->string, 0, $this->position)));
    }

    private function rest()
    {
        return substr($this->string, $this->position);
    }

    private function expecting()
    {
        ksort($this->expecting);

        return implode(', ', end($this->expecting));
    }

    public function parse($_string)
    {
        $this->cache = array();
        $this->string = $_string;
        $this->position = 0;

        $_success = $this->parsePegFile();

        if (!$_success) {
            throw new \InvalidArgumentException("Syntax error, expecting {$this->expecting()} on line {$this->line()}");
        }

        if ($this->position < strlen($this->string)) {
            throw new \InvalidArgumentException("Syntax error, unexpected {$this->rest()} on line {$this->line()}");
        }

        return $this->value;
    }
}