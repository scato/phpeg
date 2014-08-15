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
    protected $value;
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

        $_value6 = array();

        $_position2 = $this->position;

        $_value1 = array();

        $_success = $this->parse_();

        if ($_success) {
            $_value1[] = $this->value;

            $_success = $this->parseNamespace();

            if ($_success) {
                $namespace = $this->value;
            }
        }

        if ($_success) {
            $_value1[] = $this->value;

            $this->value = $_value1;
        }

        if (!$_success) {
            $_success = true;
            $this->position = $_position2;
            $this->value = null;
        }

        if ($_success) {
            $_value6[] = $this->value;

            $_value5 = array();

            while (true) {
                $_position4 = $this->position;

                $_value3 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value3[] = $this->value;

                    $_success = $this->parseImport();

                    if ($_success) {
                        $import = $this->value;
                    }
                }

                if ($_success) {
                    $_value3[] = $this->value;

                    $this->value = $_value3;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$namespace, &$import) {
                        return $import;
                    });
                }

                if (!$_success) {
                    $this->position = $_position4;

                    break;
                }

                $_value5[] = $this->value;
            }

            $_success = true;
            $this->value = $_value5;

            if ($_success) {
                $imports = $this->value;
            }
        }

        if ($_success) {
            $_value6[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value6[] = $this->value;

            $_success = $this->parseGrammar();

            if ($_success) {
                $grammar = $this->value;
            }
        }

        if ($_success) {
            $_value6[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value6[] = $this->value;

            $this->value = $_value6;
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

        $_position11 = $this->position;

        $_value10 = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $_value10[] = $this->value;

            $_value9 = array();

            while (true) {
                $_position8 = $this->position;

                $_value7 = array();

                if (substr($this->string, $this->position, 1) === '\\') {
                    $_success = true;
                    $this->value = '\\';
                    $this->position += 1;
                } else {
                    $_success = false;
                    $this->expecting[$this->position][] = '\\';
                }

                if ($_success) {
                    $_value7[] = $this->value;

                    $_success = $this->parseIdentifier();
                }

                if ($_success) {
                    $_value7[] = $this->value;

                    $this->value = $_value7;
                }

                if (!$_success) {
                    $this->position = $_position8;

                    break;
                }

                $_value9[] = $this->value;
            }

            $_success = true;
            $this->value = $_value9;
        }

        if ($_success) {
            $_value10[] = $this->value;

            $this->value = $_value10;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position11, $this->position - $_position11));
        }

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

        $_value12 = array();

        if (substr($this->string, $this->position, 9) === 'namespace') {
            $_success = true;
            $this->value = 'namespace';
            $this->position += 9;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = 'namespace';
        }

        if ($_success) {
            $_value12[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value12[] = $this->value;

            $_success = $this->parseQualifiedIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $_value12[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value12[] = $this->value;

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
            $_value12[] = $this->value;

            $this->value = $_value12;
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

        $_value13 = array();

        if (substr($this->string, $this->position, 3) === 'use') {
            $_success = true;
            $this->value = 'use';
            $this->position += 3;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = 'use';
        }

        if ($_success) {
            $_value13[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value13[] = $this->value;

            $_success = $this->parseQualifiedIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $_value13[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value13[] = $this->value;

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
            $_value13[] = $this->value;

            $this->value = $_value13;
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

        $_value21 = array();

        if (substr($this->string, $this->position, 7) === 'grammar') {
            $_success = true;
            $this->value = 'grammar';
            $this->position += 7;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = 'grammar';
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_success = $this->parseIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_position15 = $this->position;

            $_value14 = array();

            $_success = $this->parse_();

            if ($_success) {
                $_value14[] = $this->value;

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
                $_value14[] = $this->value;

                $_success = $this->parse_();
            }

            if ($_success) {
                $_value14[] = $this->value;

                $_success = $this->parseIdentifier();

                if ($_success) {
                    $base = $this->value;
                }
            }

            if ($_success) {
                $_value14[] = $this->value;

                $this->value = $_value14;
            }

            if (!$_success) {
                $_success = true;
                $this->position = $_position15;
                $this->value = null;
            }
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value21[] = $this->value;

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
            $_value21[] = $this->value;

            $_position17 = $this->position;

            $_value16 = array();

            $_success = $this->parse_();

            if ($_success) {
                $_value16[] = $this->value;

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
                $_value16[] = $this->value;

                $_success = $this->parse_();
            }

            if ($_success) {
                $_value16[] = $this->value;

                $_success = $this->parseRule();

                if ($_success) {
                    $startSymbol = $this->value;
                }
            }

            if ($_success) {
                $_value16[] = $this->value;

                $this->value = $_value16;
            }

            if (!$_success) {
                $_success = true;
                $this->position = $_position17;
                $this->value = null;
            }
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_value20 = array();

            while (true) {
                $_position19 = $this->position;

                $_value18 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value18[] = $this->value;

                    $_success = $this->parseRule();

                    if ($_success) {
                        $rule = $this->value;
                    }
                }

                if ($_success) {
                    $_value18[] = $this->value;

                    $this->value = $_value18;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$name, &$base, &$startSymbol, &$rule) {
                        return $rule;
                    });
                }

                if (!$_success) {
                    $this->position = $_position19;

                    break;
                }

                $_value20[] = $this->value;
            }

            $_success = true;
            $this->value = $_value20;

            if ($_success) {
                $rules = $this->value;
            }
        }

        if ($_success) {
            $_value21[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value21[] = $this->value;

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
            $_value21[] = $this->value;

            $this->value = $_value21;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name, &$base, &$startSymbol, &$rule, &$rules) {
                $grammar = new GrammarNode($name, array_merge(array($startSymbol), $rules));
                    if (isset($base)) $grammar->setBase($base);
                    if (isset($startSymbol)) $grammar->setStartSymbol($startSymbol->getName());
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

        $_value22 = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $name = $this->value;
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value22[] = $this->value;

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
            $_value22[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parseExpression();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value22[] = $this->value;

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
            $_value22[] = $this->value;

            $this->value = $_value22;
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

        $_position24 = $this->position;

        $_value23 = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $name = $this->value;
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value23[] = $this->value;

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
            $_value23[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value23[] = $this->value;

            $_success = $this->parsePredicate();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value23[] = $this->value;

            $this->value = $_value23;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name, &$expression) {
                return new LabelNode($name, $expression);
            });
        }

        if (!$_success) {
            $this->position = $_position24;

            $_success = $this->parsePredicate();
        }

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

        $_value28 = array();

        $_success = $this->parseLabel();

        if ($_success) {
            $first = $this->value;
        }

        if ($_success) {
            $_value28[] = $this->value;

            $_value27 = array();

            while (true) {
                $_position26 = $this->position;

                $_value25 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value25[] = $this->value;

                    $_success = $this->parseLabel();

                    if ($_success) {
                        $next = $this->value;
                    }
                }

                if ($_success) {
                    $_value25[] = $this->value;

                    $this->value = $_value25;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$first, &$next) {
                        return $next;
                    });
                }

                if (!$_success) {
                    $this->position = $_position26;

                    break;
                }

                $_value27[] = $this->value;
            }

            $_success = true;
            $this->value = $_value27;

            if ($_success) {
                $rest = $this->value;
            }
        }

        if ($_success) {
            $_value28[] = $this->value;

            $this->value = $_value28;
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

        $_position33 = $this->position;

        $_value32 = array();

        while (true) {
            $_position31 = $this->position;

            $_position30 = $this->position;

            if (preg_match('/^[^{}]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }

            if (!$_success) {
                $this->position = $_position30;

                $_value29 = array();

                if (substr($this->string, $this->position, 1) === '{') {
                    $_success = true;
                    $this->value = '{';
                    $this->position += 1;
                } else {
                    $_success = false;
                    $this->expecting[$this->position][] = '{';
                }

                if ($_success) {
                    $_value29[] = $this->value;

                    $_success = $this->parseCode();
                }

                if ($_success) {
                    $_value29[] = $this->value;

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
                    $_value29[] = $this->value;

                    $this->value = $_value29;
                }
            }

            if (!$_success) {
                $this->position = $_position31;

                break;
            }

            $_value32[] = $this->value;
        }

        $_success = true;
        $this->value = $_value32;

        if ($_success) {
            $this->value = strval(substr($this->string, $_position33, $this->position - $_position33));
        }

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

        $_position35 = $this->position;

        $_value34 = array();

        $_success = $this->parseSequence();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value34[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value34[] = $this->value;

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
            $_value34[] = $this->value;

            $_success = $this->parseCode();

            if ($_success) {
                $code = $this->value;
            }
        }

        if ($_success) {
            $_value34[] = $this->value;

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
            $_value34[] = $this->value;

            $this->value = $_value34;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression, &$code) {
                return new ActionNode($expression, trim($code));
            });
        }

        if (!$_success) {
            $this->position = $_position35;

            $_success = $this->parseSequence();
        }

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

        $_value39 = array();

        $_success = $this->parseAction();

        if ($_success) {
            $first = $this->value;
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_value38 = array();

            while (true) {
                $_position37 = $this->position;

                $_value36 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value36[] = $this->value;

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
                    $_value36[] = $this->value;

                    $_success = $this->parse_();
                }

                if ($_success) {
                    $_value36[] = $this->value;

                    $_success = $this->parseAction();

                    if ($_success) {
                        $next = $this->value;
                    }
                }

                if ($_success) {
                    $_value36[] = $this->value;

                    $this->value = $_value36;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$first, &$next) {
                        return $next;
                    });
                }

                if (!$_success) {
                    $this->position = $_position37;

                    break;
                }

                $_value38[] = $this->value;
            }

            $_success = true;
            $this->value = $_value38;

            if ($_success) {
                $rest = $this->value;
            }
        }

        if ($_success) {
            $_value39[] = $this->value;

            $this->value = $_value39;
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

        $_value40 = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value40[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value40[] = $this->value;

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
            $_value40[] = $this->value;

            $this->value = $_value40;
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

        $_value41 = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value41[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value41[] = $this->value;

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
            $_value41[] = $this->value;

            $this->value = $_value41;
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

        $_value42 = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value42[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value42[] = $this->value;

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
            $_value42[] = $this->value;

            $this->value = $_value42;
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

        $_position43 = $this->position;

        $_success = $this->parseZeroOrMore();

        if (!$_success) {
            $this->position = $_position43;

            $_success = $this->parseOneOrMore();
        }

        if (!$_success) {
            $this->position = $_position43;

            $_success = $this->parseOptional();
        }

        if (!$_success) {
            $this->position = $_position43;

            $_success = $this->parseTerminal();
        }

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

        $_value44 = array();

        if (substr($this->string, $this->position, 1) === '&') {
            $_success = true;
            $this->value = '&';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '&';
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value44[] = $this->value;

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value44[] = $this->value;

            $this->value = $_value44;
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

        $_value45 = array();

        if (substr($this->string, $this->position, 1) === '!') {
            $_success = true;
            $this->value = '!';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '!';
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value45[] = $this->value;

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value45[] = $this->value;

            $this->value = $_value45;
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

        $_value46 = array();

        if (substr($this->string, $this->position, 1) === '$') {
            $_success = true;
            $this->value = '$';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '$';
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value46[] = $this->value;

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value46[] = $this->value;

            $this->value = $_value46;
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

        $_position47 = $this->position;

        $_success = $this->parseAndPredicate();

        if (!$_success) {
            $this->position = $_position47;

            $_success = $this->parseNotPredicate();
        }

        if (!$_success) {
            $this->position = $_position47;

            $_success = $this->parseMatchedString();
        }

        if (!$_success) {
            $this->position = $_position47;

            $_success = $this->parseRepetition();
        }

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

        $_value53 = array();

        if (substr($this->string, $this->position, 1) === '"') {
            $_success = true;
            $this->value = '"';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '"';
        }

        if ($_success) {
            $_value53[] = $this->value;

            $_position52 = $this->position;

            $_value51 = array();

            while (true) {
                $_position50 = $this->position;

                $_position49 = $this->position;

                if (preg_match('/^[^\\\\"]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = $_position49;

                    $_value48 = array();

                    if (substr($this->string, $this->position, 1) === '\\') {
                        $_success = true;
                        $this->value = '\\';
                        $this->position += 1;
                    } else {
                        $_success = false;
                        $this->expecting[$this->position][] = '\\';
                    }

                    if ($_success) {
                        $_value48[] = $this->value;

                        if ($this->position < strlen($this->string)) {
                            $_success = true;
                            $this->value = substr($this->string, $this->position, 1);
                            $this->position += 1;
                        } else {
                            $_success = false;
                        }
                    }

                    if ($_success) {
                        $_value48[] = $this->value;

                        $this->value = $_value48;
                    }
                }

                if (!$_success) {
                    $this->position = $_position50;

                    break;
                }

                $_value51[] = $this->value;
            }

            $_success = true;
            $this->value = $_value51;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position52, $this->position - $_position52));
            }

            if ($_success) {
                $string = $this->value;
            }
        }

        if ($_success) {
            $_value53[] = $this->value;

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
            $_value53[] = $this->value;

            $this->value = $_value53;
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

        $_value59 = array();

        if (substr($this->string, $this->position, 1) === '[') {
            $_success = true;
            $this->value = '[';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '[';
        }

        if ($_success) {
            $_value59[] = $this->value;

            $_position58 = $this->position;

            $_value57 = array();

            while (true) {
                $_position56 = $this->position;

                $_position55 = $this->position;

                if (preg_match('/^[^\\\\\\]]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = $_position55;

                    $_value54 = array();

                    if (substr($this->string, $this->position, 1) === '\\') {
                        $_success = true;
                        $this->value = '\\';
                        $this->position += 1;
                    } else {
                        $_success = false;
                        $this->expecting[$this->position][] = '\\';
                    }

                    if ($_success) {
                        $_value54[] = $this->value;

                        if ($this->position < strlen($this->string)) {
                            $_success = true;
                            $this->value = substr($this->string, $this->position, 1);
                            $this->position += 1;
                        } else {
                            $_success = false;
                        }
                    }

                    if ($_success) {
                        $_value54[] = $this->value;

                        $this->value = $_value54;
                    }
                }

                if (!$_success) {
                    $this->position = $_position56;

                    break;
                }

                $_value57[] = $this->value;
            }

            $_success = true;
            $this->value = $_value57;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position58, $this->position - $_position58));
            }

            if ($_success) {
                $string = $this->value;
            }
        }

        if ($_success) {
            $_value59[] = $this->value;

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
            $_value59[] = $this->value;

            $this->value = $_value59;
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

        $_position66 = $this->position;

        $_value65 = array();

        $_position62 = $this->position;

        $_value61 = array();

        if (substr($this->string, $this->position, 5) === 'start') {
            $_success = true;
            $this->value = 'start';
            $this->position += 5;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = 'start';
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_position60 = $this->position;

            if (preg_match('/^[A-Za-z0-9_]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }

            if (!$_success) {
                $_success = true;
                $this->value = null;
            } else {
                $_success = false;
            }

            $this->position = $_position60;
        }

        if ($_success) {
            $_value61[] = $this->value;

            $this->value = $_value61;
        }

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position62;

        if ($_success) {
            $_value65[] = $this->value;

            if (preg_match('/^[A-Za-z_]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }
        }

        if ($_success) {
            $_value65[] = $this->value;

            $_value64 = array();

            while (true) {
                $_position63 = $this->position;

                if (preg_match('/^[A-Za-z0-9_]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = $_position63;

                    break;
                }

                $_value64[] = $this->value;
            }

            $_success = true;
            $this->value = $_value64;
        }

        if ($_success) {
            $_value65[] = $this->value;

            $this->value = $_value65;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position66, $this->position - $_position66));
        }

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

        $_value67 = array();

        if (substr($this->string, $this->position, 1) === '(') {
            $_success = true;
            $this->value = '(';
            $this->position += 1;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '(';
        }

        if ($_success) {
            $_value67[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value67[] = $this->value;

            $_success = $this->parseExpression();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value67[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value67[] = $this->value;

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
            $_value67[] = $this->value;

            $this->value = $_value67;
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

        $_position68 = $this->position;

        $_success = $this->parseRuleReference();

        if (!$_success) {
            $this->position = $_position68;

            $_success = $this->parseLiteral();
        }

        if (!$_success) {
            $this->position = $_position68;

            $_success = $this->parseAny();
        }

        if (!$_success) {
            $this->position = $_position68;

            $_success = $this->parseCharacterClass();
        }

        if (!$_success) {
            $this->position = $_position68;

            $_success = $this->parseSubExpression();
        }

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

        $_value71 = array();

        while (true) {
            $_position70 = $this->position;

            $_position69 = $this->position;

            $_success = $this->parseWhitespace();

            if (!$_success) {
                $this->position = $_position69;

                $_success = $this->parseBlockComment();
            }

            if (!$_success) {
                $this->position = $_position69;

                $_success = $this->parseInlineComment();
            }

            if (!$_success) {
                $this->position = $_position70;

                break;
            }

            $_value71[] = $this->value;
        }

        $_success = true;
        $this->value = $_value71;

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

        $_value76 = array();

        if (substr($this->string, $this->position, 2) === '/*') {
            $_success = true;
            $this->value = '/*';
            $this->position += 2;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '/*';
        }

        if ($_success) {
            $_value76[] = $this->value;

            $_value75 = array();

            while (true) {
                $_position74 = $this->position;

                $_value73 = array();

                $_position72 = $this->position;

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

                $this->position = $_position72;

                if ($_success) {
                    $_value73[] = $this->value;

                    if ($this->position < strlen($this->string)) {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, 1);
                        $this->position += 1;
                    } else {
                        $_success = false;
                    }
                }

                if ($_success) {
                    $_value73[] = $this->value;

                    $this->value = $_value73;
                }

                if (!$_success) {
                    $this->position = $_position74;

                    break;
                }

                $_value75[] = $this->value;
            }

            $_success = true;
            $this->value = $_value75;
        }

        if ($_success) {
            $_value76[] = $this->value;

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
            $_value76[] = $this->value;

            $this->value = $_value76;
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

        $_value79 = array();

        if (substr($this->string, $this->position, 2) === '//') {
            $_success = true;
            $this->value = '//';
            $this->position += 2;
        } else {
            $_success = false;
            $this->expecting[$this->position][] = '//';
        }

        if ($_success) {
            $_value79[] = $this->value;

            $_value78 = array();

            while (true) {
                $_position77 = $this->position;

                if (preg_match('/^[^\\r\\n]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    $this->position = $_position77;

                    break;
                }

                $_value78[] = $this->value;
            }

            $_success = true;
            $this->value = $_value78;
        }

        if ($_success) {
            $_value79[] = $this->value;

            $this->value = $_value79;
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