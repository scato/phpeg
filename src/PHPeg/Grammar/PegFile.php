<?php

namespace PHPeg\Grammar;

use PHPeg\Grammar\Tree\ActionNode;
use PHPeg\Grammar\Tree\AndActionNode;
use PHPeg\Grammar\Tree\AndPredicateNode;
use PHPeg\Grammar\Tree\AnyNode;
use PHPeg\Grammar\Tree\CharacterClassNode;
use PHPeg\Grammar\Tree\ChoiceNode;
use PHPeg\Grammar\Tree\CutNode;
use PHPeg\Grammar\Tree\GrammarNode;
use PHPeg\Grammar\Tree\LabelNode;
use PHPeg\Grammar\Tree\LiteralNode;
use PHPeg\Grammar\Tree\MatchedStringNode;
use PHPeg\Grammar\Tree\NotActionNode;
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
    protected $cut = false;
    protected $cache;
    protected $errors = array();
    protected $warnings = array();

    protected function parsePegFile()
    {
        $_position = $this->position;

        if (isset($this->cache['PegFile'][$_position])) {
            $_success = $this->cache['PegFile'][$_position]['success'];
            $this->position = $this->cache['PegFile'][$_position]['position'];
            $this->value = $this->cache['PegFile'][$_position]['value'];

            return $_success;
        }

        $_value8 = array();

        $_position2 = $this->position;
        $_cut3 = $this->cut;

        $this->cut = false;
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

        if (!$_success && !$this->cut) {
            $_success = true;
            $this->position = $_position2;
            $this->value = null;
        }

        $this->cut = $_cut3;

        if ($_success) {
            $_value8[] = $this->value;

            $_value6 = array();
            $_cut7 = $this->cut;

            while (true) {
                $_position5 = $this->position;

                $this->cut = false;
                $_value4 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value4[] = $this->value;

                    $_success = $this->parseImport();

                    if ($_success) {
                        $import = $this->value;
                    }
                }

                if ($_success) {
                    $_value4[] = $this->value;

                    $this->value = $_value4;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$namespace, &$import) {
                        return $import;
                    });
                }

                if (!$_success) {
                    break;
                }

                $_value6[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position5;
                $this->value = $_value6;
            }

            $this->cut = $_cut7;

            if ($_success) {
                $imports = $this->value;
            }
        }

        if ($_success) {
            $_value8[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value8[] = $this->value;

            $_success = $this->parseGrammar();

            if ($_success) {
                $grammar = $this->value;
            }
        }

        if ($_success) {
            $_value8[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value8[] = $this->value;

            $this->value = $_value8;
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
            $this->report($_position, 'PegFile');
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

        $_position14 = $this->position;

        $_value13 = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $_value13[] = $this->value;

            $_value11 = array();
            $_cut12 = $this->cut;

            while (true) {
                $_position10 = $this->position;

                $this->cut = false;
                $_value9 = array();

                if (substr($this->string, $this->position, strlen("\\")) === "\\") {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen("\\"));
                    $this->position += strlen("\\");
                } else {
                    $_success = false;

                    $this->report($this->position, '"\\\\"');
                }

                if ($_success) {
                    $_value9[] = $this->value;

                    $_success = true;
                    $this->value = null;

                    $this->cut = true;
                }

                if ($_success) {
                    $_value9[] = $this->value;

                    $_success = $this->parseIdentifier();
                }

                if ($_success) {
                    $_value9[] = $this->value;

                    $this->value = $_value9;
                }

                if (!$_success) {
                    break;
                }

                $_value11[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position10;
                $this->value = $_value11;
            }

            $this->cut = $_cut12;
        }

        if ($_success) {
            $_value13[] = $this->value;

            $this->value = $_value13;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position14, $this->position - $_position14));
        }

        $this->cache['QualifiedIdentifier'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'QualifiedIdentifier');
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

        $_value15 = array();

        if (substr($this->string, $this->position, strlen("namespace")) === "namespace") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("namespace"));
            $this->position += strlen("namespace");
        } else {
            $_success = false;

            $this->report($this->position, '"namespace"');
        }

        if ($_success) {
            $_value15[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value15[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value15[] = $this->value;

            $_success = $this->parseQualifiedIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $_value15[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value15[] = $this->value;

            if (substr($this->string, $this->position, strlen(";")) === ";") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(";"));
                $this->position += strlen(";");
            } else {
                $_success = false;

                $this->report($this->position, '";"');
            }
        }

        if ($_success) {
            $_value15[] = $this->value;

            $this->value = $_value15;
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
            $this->report($_position, 'Namespace');
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

        $_value16 = array();

        if (substr($this->string, $this->position, strlen("use")) === "use") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("use"));
            $this->position += strlen("use");
        } else {
            $_success = false;

            $this->report($this->position, '"use"');
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = $this->parseQualifiedIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $_value16[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value16[] = $this->value;

            if (substr($this->string, $this->position, strlen(";")) === ";") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(";"));
                $this->position += strlen(";");
            } else {
                $_success = false;

                $this->report($this->position, '";"');
            }
        }

        if ($_success) {
            $_value16[] = $this->value;

            $this->value = $_value16;
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
            $this->report($_position, 'Import');
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

        $_value27 = array();

        if (substr($this->string, $this->position, strlen("grammar")) === "grammar") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("grammar"));
            $this->position += strlen("grammar");
        } else {
            $_success = false;

            $this->report($this->position, '"grammar"');
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parseIdentifier();

            if ($_success) {
                $name = $this->value;
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_position18 = $this->position;
            $_cut19 = $this->cut;

            $this->cut = false;
            $_value17 = array();

            $_success = $this->parse_();

            if ($_success) {
                $_value17[] = $this->value;

                if (substr($this->string, $this->position, strlen("extends")) === "extends") {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen("extends"));
                    $this->position += strlen("extends");
                } else {
                    $_success = false;

                    $this->report($this->position, '"extends"');
                }
            }

            if ($_success) {
                $_value17[] = $this->value;

                $_success = $this->parse_();
            }

            if ($_success) {
                $_value17[] = $this->value;

                $_success = $this->parseIdentifier();

                if ($_success) {
                    $base = $this->value;
                }
            }

            if ($_success) {
                $_value17[] = $this->value;

                $this->value = $_value17;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position18;
                $this->value = null;
            }

            $this->cut = $_cut19;
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value27[] = $this->value;

            if (substr($this->string, $this->position, strlen("{")) === "{") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("{"));
                $this->position += strlen("{");
            } else {
                $_success = false;

                $this->report($this->position, '"{"');
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_position21 = $this->position;
            $_cut22 = $this->cut;

            $this->cut = false;
            $_value20 = array();

            $_success = $this->parse_();

            if ($_success) {
                $_value20[] = $this->value;

                if (substr($this->string, $this->position, strlen("start")) === "start") {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen("start"));
                    $this->position += strlen("start");
                } else {
                    $_success = false;

                    $this->report($this->position, '"start"');
                }
            }

            if ($_success) {
                $_value20[] = $this->value;

                $_success = $this->parse_();
            }

            if ($_success) {
                $_value20[] = $this->value;

                $_success = $this->parseRule();

                if ($_success) {
                    $startSymbol = $this->value;
                }
            }

            if ($_success) {
                $_value20[] = $this->value;

                $this->value = $_value20;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position21;
                $this->value = null;
            }

            $this->cut = $_cut22;
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_value25 = array();
            $_cut26 = $this->cut;

            while (true) {
                $_position24 = $this->position;

                $this->cut = false;
                $_value23 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value23[] = $this->value;

                    $_success = $this->parseRule();

                    if ($_success) {
                        $rule = $this->value;
                    }
                }

                if ($_success) {
                    $_value23[] = $this->value;

                    $this->value = $_value23;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$name, &$base, &$startSymbol, &$rule) {
                        return $rule;
                    });
                }

                if (!$_success) {
                    break;
                }

                $_value25[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position24;
                $this->value = $_value25;
            }

            $this->cut = $_cut26;

            if ($_success) {
                $rules = $this->value;
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value27[] = $this->value;

            if (substr($this->string, $this->position, strlen("}")) === "}") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("}"));
                $this->position += strlen("}");
            } else {
                $_success = false;

                $this->report($this->position, '"}"');
            }
        }

        if ($_success) {
            $_value27[] = $this->value;

            $this->value = $_value27;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name, &$base, &$startSymbol, &$rule, &$rules) {
                $rules = array_merge(isset($startSymbol) ? array($startSymbol) : array(), $rules);
                    $grammar = new GrammarNode($name, $rules);
                    if (isset($base)) $grammar->setBase($base);
                    if (isset($startSymbol)) $grammar->setStartSymbol($startSymbol->getIdentifier());
                    return $grammar;
            });
        }

        $this->cache['Grammar'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Grammar');
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

        $_value31 = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $identifier = $this->value;
        }

        if ($_success) {
            $_value31[] = $this->value;

            $_position29 = $this->position;
            $_cut30 = $this->cut;

            $this->cut = false;
            $_value28 = array();

            $_success = $this->parse_();

            if ($_success) {
                $_value28[] = $this->value;

                $_success = $this->parseString();

                if ($_success) {
                    $name = $this->value;
                }
            }

            if ($_success) {
                $_value28[] = $this->value;

                $this->value = $_value28;
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position29;
                $this->value = null;
            }

            $this->cut = $_cut30;
        }

        if ($_success) {
            $_value31[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value31[] = $this->value;

            if (substr($this->string, $this->position, strlen("=")) === "=") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("="));
                $this->position += strlen("=");
            } else {
                $_success = false;

                $this->report($this->position, '"="');
            }
        }

        if ($_success) {
            $_value31[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value31[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value31[] = $this->value;

            $_success = $this->parseExpression();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value31[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value31[] = $this->value;

            if (substr($this->string, $this->position, strlen(";")) === ";") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(";"));
                $this->position += strlen(";");
            } else {
                $_success = false;

                $this->report($this->position, '";"');
            }
        }

        if ($_success) {
            $_value31[] = $this->value;

            $this->value = $_value31;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$identifier, &$name, &$expression) {
                return new RuleNode($identifier, isset($name) ? $name : "'$identifier'", $expression);
            });
        }

        $this->cache['Rule'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Rule');
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

        $_position33 = $this->position;
        $_cut34 = $this->cut;

        $this->cut = false;
        $_value32 = array();

        $_success = $this->parseIdentifier();

        if ($_success) {
            $name = $this->value;
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value32[] = $this->value;

            if (substr($this->string, $this->position, strlen(":")) === ":") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(":"));
                $this->position += strlen(":");
            } else {
                $_success = false;

                $this->report($this->position, '":"');
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value32[] = $this->value;

            $_success = $this->parsePredicate();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value32[] = $this->value;

            $this->value = $_value32;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$name, &$expression) {
                return new LabelNode($name, $expression);
            });
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position33;

            $_success = $this->parsePredicate();
        }

        $this->cut = $_cut34;

        $this->cache['Label'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Label');
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

        $_value39 = array();

        $_success = $this->parseLabel();

        if ($_success) {
            $first = $this->value;
        }

        if ($_success) {
            $_value39[] = $this->value;

            $_value37 = array();
            $_cut38 = $this->cut;

            while (true) {
                $_position36 = $this->position;

                $this->cut = false;
                $_value35 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value35[] = $this->value;

                    $_success = $this->parseLabel();

                    if ($_success) {
                        $next = $this->value;
                    }
                }

                if ($_success) {
                    $_value35[] = $this->value;

                    $this->value = $_value35;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$first, &$next) {
                        return $next;
                    });
                }

                if (!$_success) {
                    break;
                }

                $_value37[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position36;
                $this->value = $_value37;
            }

            $this->cut = $_cut38;

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
                return empty($rest) ? $first : new SequenceNode(array_merge(array($first), $rest));
            });
        }

        $this->cache['Sequence'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Sequence');
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

        $_position46 = $this->position;

        $_value44 = array();
        $_cut45 = $this->cut;

        while (true) {
            $_position43 = $this->position;

            $this->cut = false;
            $_position41 = $this->position;
            $_cut42 = $this->cut;

            $this->cut = false;
            if (preg_match('/^[^{}]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position41;

                $_value40 = array();

                if (substr($this->string, $this->position, strlen("{")) === "{") {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen("{"));
                    $this->position += strlen("{");
                } else {
                    $_success = false;

                    $this->report($this->position, '"{"');
                }

                if ($_success) {
                    $_value40[] = $this->value;

                    $_success = $this->parseCode();
                }

                if ($_success) {
                    $_value40[] = $this->value;

                    if (substr($this->string, $this->position, strlen("}")) === "}") {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, strlen("}"));
                        $this->position += strlen("}");
                    } else {
                        $_success = false;

                        $this->report($this->position, '"}"');
                    }
                }

                if ($_success) {
                    $_value40[] = $this->value;

                    $this->value = $_value40;
                }
            }

            $this->cut = $_cut42;

            if (!$_success) {
                break;
            }

            $_value44[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position43;
            $this->value = $_value44;
        }

        $this->cut = $_cut45;

        if ($_success) {
            $this->value = strval(substr($this->string, $_position46, $this->position - $_position46));
        }

        $this->cache['Code'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Code');
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

        $_position48 = $this->position;
        $_cut49 = $this->cut;

        $this->cut = false;
        $_value47 = array();

        $_success = $this->parseSequence();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value47[] = $this->value;

            if (substr($this->string, $this->position, strlen("{")) === "{") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("{"));
                $this->position += strlen("{");
            } else {
                $_success = false;

                $this->report($this->position, '"{"');
            }
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value47[] = $this->value;

            $_success = $this->parseCode();

            if ($_success) {
                $code = $this->value;
            }
        }

        if ($_success) {
            $_value47[] = $this->value;

            if (substr($this->string, $this->position, strlen("}")) === "}") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("}"));
                $this->position += strlen("}");
            } else {
                $_success = false;

                $this->report($this->position, '"}"');
            }
        }

        if ($_success) {
            $_value47[] = $this->value;

            $this->value = $_value47;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$expression, &$code) {
                return new ActionNode($expression, trim($code));
            });
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position48;

            $_success = $this->parseSequence();
        }

        $this->cut = $_cut49;

        $this->cache['Action'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Action');
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

        $_value54 = array();

        $_success = $this->parseAction();

        if ($_success) {
            $first = $this->value;
        }

        if ($_success) {
            $_value54[] = $this->value;

            $_value52 = array();
            $_cut53 = $this->cut;

            while (true) {
                $_position51 = $this->position;

                $this->cut = false;
                $_value50 = array();

                $_success = $this->parse_();

                if ($_success) {
                    $_value50[] = $this->value;

                    if (substr($this->string, $this->position, strlen("/")) === "/") {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, strlen("/"));
                        $this->position += strlen("/");
                    } else {
                        $_success = false;

                        $this->report($this->position, '"/"');
                    }
                }

                if ($_success) {
                    $_value50[] = $this->value;

                    $_success = true;
                    $this->value = null;

                    $this->cut = true;
                }

                if ($_success) {
                    $_value50[] = $this->value;

                    $_success = $this->parse_();
                }

                if ($_success) {
                    $_value50[] = $this->value;

                    $_success = $this->parseAction();

                    if ($_success) {
                        $next = $this->value;
                    }
                }

                if ($_success) {
                    $_value50[] = $this->value;

                    $this->value = $_value50;
                }

                if ($_success) {
                    $this->value = call_user_func(function () use (&$first, &$next) {
                        return $next;
                    });
                }

                if (!$_success) {
                    break;
                }

                $_value52[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position51;
                $this->value = $_value52;
            }

            $this->cut = $_cut53;

            if ($_success) {
                $rest = $this->value;
            }
        }

        if ($_success) {
            $_value54[] = $this->value;

            $this->value = $_value54;
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
            $this->report($_position, 'Choice');
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
            $this->report($_position, 'Expression');
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

        $_value55 = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value55[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value55[] = $this->value;

            if (substr($this->string, $this->position, strlen("*")) === "*") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("*"));
                $this->position += strlen("*");
            } else {
                $_success = false;

                $this->report($this->position, '"*"');
            }
        }

        if ($_success) {
            $_value55[] = $this->value;

            $this->value = $_value55;
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
            $this->report($_position, 'ZeroOrMore');
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

        $_value56 = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value56[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value56[] = $this->value;

            if (substr($this->string, $this->position, strlen("+")) === "+") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("+"));
                $this->position += strlen("+");
            } else {
                $_success = false;

                $this->report($this->position, '"+"');
            }
        }

        if ($_success) {
            $_value56[] = $this->value;

            $this->value = $_value56;
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
            $this->report($_position, 'OneOrMore');
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

        $_value57 = array();

        $_success = $this->parseTerminal();

        if ($_success) {
            $expression = $this->value;
        }

        if ($_success) {
            $_value57[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value57[] = $this->value;

            if (substr($this->string, $this->position, strlen("?")) === "?") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("?"));
                $this->position += strlen("?");
            } else {
                $_success = false;

                $this->report($this->position, '"?"');
            }
        }

        if ($_success) {
            $_value57[] = $this->value;

            $this->value = $_value57;
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
            $this->report($_position, 'Optional');
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

        $_position58 = $this->position;
        $_cut59 = $this->cut;

        $this->cut = false;
        $_success = $this->parseZeroOrMore();

        if (!$_success && !$this->cut) {
            $this->position = $_position58;

            $_success = $this->parseOneOrMore();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position58;

            $_success = $this->parseOptional();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position58;

            $_success = $this->parseTerminal();
        }

        $this->cut = $_cut59;

        $this->cache['Repetition'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Repetition');
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

        $_value60 = array();

        if (substr($this->string, $this->position, strlen("&")) === "&") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("&"));
            $this->position += strlen("&");
        } else {
            $_success = false;

            $this->report($this->position, '"&"');
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value60[] = $this->value;

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value60[] = $this->value;

            $this->value = $_value60;
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
            $this->report($_position, 'AndPredicate');
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

        $_value61 = array();

        if (substr($this->string, $this->position, strlen("!")) === "!") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("!"));
            $this->position += strlen("!");
        } else {
            $_success = false;

            $this->report($this->position, '"!"');
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value61[] = $this->value;

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value61[] = $this->value;

            $this->value = $_value61;
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
            $this->report($_position, 'NotPredicate');
        }

        return $_success;
    }

    protected function parseAndAction()
    {
        $_position = $this->position;

        if (isset($this->cache['AndAction'][$_position])) {
            $_success = $this->cache['AndAction'][$_position]['success'];
            $this->position = $this->cache['AndAction'][$_position]['position'];
            $this->value = $this->cache['AndAction'][$_position]['value'];

            return $_success;
        }

        $_value62 = array();

        if (substr($this->string, $this->position, strlen("&")) === "&") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("&"));
            $this->position += strlen("&");
        } else {
            $_success = false;

            $this->report($this->position, '"&"');
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value62[] = $this->value;

            if (substr($this->string, $this->position, strlen("{")) === "{") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("{"));
                $this->position += strlen("{");
            } else {
                $_success = false;

                $this->report($this->position, '"{"');
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value62[] = $this->value;

            $_success = $this->parseCode();

            if ($_success) {
                $code = $this->value;
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            if (substr($this->string, $this->position, strlen("}")) === "}") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("}"));
                $this->position += strlen("}");
            } else {
                $_success = false;

                $this->report($this->position, '"}"');
            }
        }

        if ($_success) {
            $_value62[] = $this->value;

            $this->value = $_value62;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$code) {
                return new AndActionNode(trim($code));
            });
        }

        $this->cache['AndAction'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'AndAction');
        }

        return $_success;
    }

    protected function parseNotAction()
    {
        $_position = $this->position;

        if (isset($this->cache['NotAction'][$_position])) {
            $_success = $this->cache['NotAction'][$_position]['success'];
            $this->position = $this->cache['NotAction'][$_position]['position'];
            $this->value = $this->cache['NotAction'][$_position]['value'];

            return $_success;
        }

        $_value63 = array();

        if (substr($this->string, $this->position, strlen("!")) === "!") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("!"));
            $this->position += strlen("!");
        } else {
            $_success = false;

            $this->report($this->position, '"!"');
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value63[] = $this->value;

            if (substr($this->string, $this->position, strlen("{")) === "{") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("{"));
                $this->position += strlen("{");
            } else {
                $_success = false;

                $this->report($this->position, '"{"');
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value63[] = $this->value;

            $_success = $this->parseCode();

            if ($_success) {
                $code = $this->value;
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            if (substr($this->string, $this->position, strlen("}")) === "}") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("}"));
                $this->position += strlen("}");
            } else {
                $_success = false;

                $this->report($this->position, '"}"');
            }
        }

        if ($_success) {
            $_value63[] = $this->value;

            $this->value = $_value63;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$code) {
                return new NotActionNode(trim($code));
            });
        }

        $this->cache['NotAction'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'NotAction');
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

        $_value64 = array();

        if (substr($this->string, $this->position, strlen("$")) === "$") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("$"));
            $this->position += strlen("$");
        } else {
            $_success = false;

            $this->report($this->position, '"$"');
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value64[] = $this->value;

            $_success = $this->parseRepetition();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value64[] = $this->value;

            $this->value = $_value64;
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
            $this->report($_position, 'MatchedString');
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

        $_position65 = $this->position;
        $_cut66 = $this->cut;

        $this->cut = false;
        $_success = $this->parseAndAction();

        if (!$_success && !$this->cut) {
            $this->position = $_position65;

            $_success = $this->parseNotAction();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position65;

            $_success = $this->parseAndPredicate();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position65;

            $_success = $this->parseNotPredicate();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position65;

            $_success = $this->parseMatchedString();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position65;

            $_success = $this->parseRepetition();
        }

        $this->cut = $_cut66;

        $this->cache['Predicate'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Predicate');
        }

        return $_success;
    }

    protected function parseString()
    {
        $_position = $this->position;

        if (isset($this->cache['String'][$_position])) {
            $_success = $this->cache['String'][$_position]['success'];
            $this->position = $this->cache['String'][$_position]['position'];
            $this->value = $this->cache['String'][$_position]['value'];

            return $_success;
        }

        $_position83 = $this->position;

        $_position81 = $this->position;
        $_cut82 = $this->cut;

        $this->cut = false;
        $_value73 = array();

        if (substr($this->string, $this->position, strlen("\"")) === "\"") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("\""));
            $this->position += strlen("\"");
        } else {
            $_success = false;

            $this->report($this->position, '"\\""');
        }

        if ($_success) {
            $_value73[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value73[] = $this->value;

            $_value71 = array();
            $_cut72 = $this->cut;

            while (true) {
                $_position70 = $this->position;

                $this->cut = false;
                $_position68 = $this->position;
                $_cut69 = $this->cut;

                $this->cut = false;
                if (preg_match('/^[^\\\\"]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success && !$this->cut) {
                    $this->position = $_position68;

                    $_value67 = array();

                    if (substr($this->string, $this->position, strlen("\\")) === "\\") {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, strlen("\\"));
                        $this->position += strlen("\\");
                    } else {
                        $_success = false;

                        $this->report($this->position, '"\\\\"');
                    }

                    if ($_success) {
                        $_value67[] = $this->value;

                        if ($this->position < strlen($this->string)) {
                            $_success = true;
                            $this->value = substr($this->string, $this->position, 1);
                            $this->position += 1;
                        } else {
                            $_success = false;
                        }
                    }

                    if ($_success) {
                        $_value67[] = $this->value;

                        $this->value = $_value67;
                    }
                }

                $this->cut = $_cut69;

                if (!$_success) {
                    break;
                }

                $_value71[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position70;
                $this->value = $_value71;
            }

            $this->cut = $_cut72;
        }

        if ($_success) {
            $_value73[] = $this->value;

            if (substr($this->string, $this->position, strlen("\"")) === "\"") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("\""));
                $this->position += strlen("\"");
            } else {
                $_success = false;

                $this->report($this->position, '"\\""');
            }
        }

        if ($_success) {
            $_value73[] = $this->value;

            $this->value = $_value73;
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position81;

            $_value80 = array();

            if (substr($this->string, $this->position, strlen("'")) === "'") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("'"));
                $this->position += strlen("'");
            } else {
                $_success = false;

                $this->report($this->position, '"\'"');
            }

            if ($_success) {
                $_value80[] = $this->value;

                $_success = true;
                $this->value = null;

                $this->cut = true;
            }

            if ($_success) {
                $_value80[] = $this->value;

                $_value78 = array();
                $_cut79 = $this->cut;

                while (true) {
                    $_position77 = $this->position;

                    $this->cut = false;
                    $_position75 = $this->position;
                    $_cut76 = $this->cut;

                    $this->cut = false;
                    if (preg_match('/^[^\\\\\']$/', substr($this->string, $this->position, 1))) {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, 1);
                        $this->position += 1;
                    } else {
                        $_success = false;
                    }

                    if (!$_success && !$this->cut) {
                        $this->position = $_position75;

                        $_value74 = array();

                        if (substr($this->string, $this->position, strlen("\\")) === "\\") {
                            $_success = true;
                            $this->value = substr($this->string, $this->position, strlen("\\"));
                            $this->position += strlen("\\");
                        } else {
                            $_success = false;

                            $this->report($this->position, '"\\\\"');
                        }

                        if ($_success) {
                            $_value74[] = $this->value;

                            if ($this->position < strlen($this->string)) {
                                $_success = true;
                                $this->value = substr($this->string, $this->position, 1);
                                $this->position += 1;
                            } else {
                                $_success = false;
                            }
                        }

                        if ($_success) {
                            $_value74[] = $this->value;

                            $this->value = $_value74;
                        }
                    }

                    $this->cut = $_cut76;

                    if (!$_success) {
                        break;
                    }

                    $_value78[] = $this->value;
                }

                if (!$this->cut) {
                    $_success = true;
                    $this->position = $_position77;
                    $this->value = $_value78;
                }

                $this->cut = $_cut79;
            }

            if ($_success) {
                $_value80[] = $this->value;

                if (substr($this->string, $this->position, strlen("'")) === "'") {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen("'"));
                    $this->position += strlen("'");
                } else {
                    $_success = false;

                    $this->report($this->position, '"\'"');
                }
            }

            if ($_success) {
                $_value80[] = $this->value;

                $this->value = $_value80;
            }
        }

        $this->cut = $_cut82;

        if ($_success) {
            $this->value = strval(substr($this->string, $_position83, $this->position - $_position83));
        }

        $this->cache['String'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'String');
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

        $_value86 = array();

        $_success = $this->parseString();

        if ($_success) {
            $string = $this->value;
        }

        if ($_success) {
            $_value86[] = $this->value;

            $_position84 = $this->position;
            $_cut85 = $this->cut;

            $this->cut = false;
            if (substr($this->string, $this->position, strlen("i")) === "i") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("i"));
                $this->position += strlen("i");
            } else {
                $_success = false;

                $this->report($this->position, '"i"');
            }

            if (!$_success && !$this->cut) {
                $_success = true;
                $this->position = $_position84;
                $this->value = null;
            }

            $this->cut = $_cut85;

            if ($_success) {
                $ci = $this->value;
            }
        }

        if ($_success) {
            $_value86[] = $this->value;

            $this->value = $_value86;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$string, &$ci) {
                return new LiteralNode($string, isset($ci));
            });
        }

        $this->cache['Literal'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Literal');
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

        if (substr($this->string, $this->position, strlen(".")) === ".") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("."));
            $this->position += strlen(".");
        } else {
            $_success = false;

            $this->report($this->position, '"."');
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
            $this->report($_position, 'Any');
        }

        return $_success;
    }

    protected function parseCut()
    {
        $_position = $this->position;

        if (isset($this->cache['Cut'][$_position])) {
            $_success = $this->cache['Cut'][$_position]['success'];
            $this->position = $this->cache['Cut'][$_position]['position'];
            $this->value = $this->cache['Cut'][$_position]['value'];

            return $_success;
        }

        if (substr($this->string, $this->position, strlen("^")) === "^") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("^"));
            $this->position += strlen("^");
        } else {
            $_success = false;

            $this->report($this->position, '"^"');
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return new CutNode();
            });
        }

        $this->cache['Cut'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Cut');
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

        $_value94 = array();

        if (substr($this->string, $this->position, strlen("[")) === "[") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("["));
            $this->position += strlen("[");
        } else {
            $_success = false;

            $this->report($this->position, '"["');
        }

        if ($_success) {
            $_value94[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value94[] = $this->value;

            $_position93 = $this->position;

            $_value91 = array();
            $_cut92 = $this->cut;

            while (true) {
                $_position90 = $this->position;

                $this->cut = false;
                $_position88 = $this->position;
                $_cut89 = $this->cut;

                $this->cut = false;
                if (preg_match('/^[^\\\\\\]]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success && !$this->cut) {
                    $this->position = $_position88;

                    $_value87 = array();

                    if (substr($this->string, $this->position, strlen("\\")) === "\\") {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, strlen("\\"));
                        $this->position += strlen("\\");
                    } else {
                        $_success = false;

                        $this->report($this->position, '"\\\\"');
                    }

                    if ($_success) {
                        $_value87[] = $this->value;

                        if ($this->position < strlen($this->string)) {
                            $_success = true;
                            $this->value = substr($this->string, $this->position, 1);
                            $this->position += 1;
                        } else {
                            $_success = false;
                        }
                    }

                    if ($_success) {
                        $_value87[] = $this->value;

                        $this->value = $_value87;
                    }
                }

                $this->cut = $_cut89;

                if (!$_success) {
                    break;
                }

                $_value91[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position90;
                $this->value = $_value91;
            }

            $this->cut = $_cut92;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position93, $this->position - $_position93));
            }

            if ($_success) {
                $string = $this->value;
            }
        }

        if ($_success) {
            $_value94[] = $this->value;

            if (substr($this->string, $this->position, strlen("]")) === "]") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("]"));
                $this->position += strlen("]");
            } else {
                $_success = false;

                $this->report($this->position, '"]"');
            }
        }

        if ($_success) {
            $_value94[] = $this->value;

            $this->value = $_value94;
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
            $this->report($_position, 'CharacterClass');
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

        $_position102 = $this->position;

        $_value101 = array();

        $_position97 = $this->position;

        $_value96 = array();

        if (substr($this->string, $this->position, strlen("start")) === "start") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("start"));
            $this->position += strlen("start");
        } else {
            $_success = false;

            $this->report($this->position, '"start"');
        }

        if ($_success) {
            $_value96[] = $this->value;

            $_position95 = $this->position;

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

            $this->position = $_position95;
        }

        if ($_success) {
            $_value96[] = $this->value;

            $this->value = $_value96;
        }

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position97;

        if ($_success) {
            $_value101[] = $this->value;

            if (preg_match('/^[A-Za-z_]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }
        }

        if ($_success) {
            $_value101[] = $this->value;

            $_value99 = array();
            $_cut100 = $this->cut;

            while (true) {
                $_position98 = $this->position;

                $this->cut = false;
                if (preg_match('/^[A-Za-z0-9_]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    break;
                }

                $_value99[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position98;
                $this->value = $_value99;
            }

            $this->cut = $_cut100;
        }

        if ($_success) {
            $_value101[] = $this->value;

            $this->value = $_value101;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position102, $this->position - $_position102));
        }

        $this->cache['Identifier'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Identifier');
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
            $this->report($_position, 'RuleReference');
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

        $_value103 = array();

        if (substr($this->string, $this->position, strlen("(")) === "(") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("("));
            $this->position += strlen("(");
        } else {
            $_success = false;

            $this->report($this->position, '"("');
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parseExpression();

            if ($_success) {
                $expression = $this->value;
            }
        }

        if ($_success) {
            $_value103[] = $this->value;

            $_success = $this->parse_();
        }

        if ($_success) {
            $_value103[] = $this->value;

            if (substr($this->string, $this->position, strlen(")")) === ")") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen(")"));
                $this->position += strlen(")");
            } else {
                $_success = false;

                $this->report($this->position, '")"');
            }
        }

        if ($_success) {
            $_value103[] = $this->value;

            $this->value = $_value103;
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
            $this->report($_position, 'SubExpression');
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

        $_position104 = $this->position;
        $_cut105 = $this->cut;

        $this->cut = false;
        $_success = $this->parseRuleReference();

        if (!$_success && !$this->cut) {
            $this->position = $_position104;

            $_success = $this->parseLiteral();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position104;

            $_success = $this->parseAny();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position104;

            $_success = $this->parseCut();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position104;

            $_success = $this->parseCharacterClass();
        }

        if (!$_success && !$this->cut) {
            $this->position = $_position104;

            $_success = $this->parseSubExpression();
        }

        $this->cut = $_cut105;

        $this->cache['Terminal'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'Terminal');
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

        $_value111 = array();
        $_cut112 = $this->cut;

        while (true) {
            $_position110 = $this->position;

            $this->cut = false;
            $_value109 = array();

            $_position106 = $this->position;

            if (preg_match('/^[ \\n\\r\\t\\/]$/', substr($this->string, $this->position, 1))) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }

            if ($_success) {
                $this->value = null;
            }

            $this->position = $_position106;

            if ($_success) {
                $_value109[] = $this->value;

                $_position107 = $this->position;
                $_cut108 = $this->cut;

                $this->cut = false;
                $_success = $this->parseWhitespace();

                if (!$_success && !$this->cut) {
                    $this->position = $_position107;

                    $_success = $this->parseBlockComment();
                }

                if (!$_success && !$this->cut) {
                    $this->position = $_position107;

                    $_success = $this->parseInlineComment();
                }

                $this->cut = $_cut108;
            }

            if ($_success) {
                $_value109[] = $this->value;

                $this->value = $_value109;
            }

            if (!$_success) {
                break;
            }

            $_value111[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position110;
            $this->value = $_value111;
        }

        $this->cut = $_cut112;

        $this->cache['_'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, '_');
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
            $this->report($_position, 'Whitespace');
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

        $_value118 = array();

        if (substr($this->string, $this->position, strlen("/*")) === "/*") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("/*"));
            $this->position += strlen("/*");
        } else {
            $_success = false;

            $this->report($this->position, '"/*"');
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value118[] = $this->value;

            $_value116 = array();
            $_cut117 = $this->cut;

            while (true) {
                $_position115 = $this->position;

                $this->cut = false;
                $_value114 = array();

                $_position113 = $this->position;

                if (substr($this->string, $this->position, strlen("*/")) === "*/") {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen("*/"));
                    $this->position += strlen("*/");
                } else {
                    $_success = false;

                    $this->report($this->position, '"*/"');
                }

                if (!$_success) {
                    $_success = true;
                    $this->value = null;
                } else {
                    $_success = false;
                }

                $this->position = $_position113;

                if ($_success) {
                    $_value114[] = $this->value;

                    if ($this->position < strlen($this->string)) {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, 1);
                        $this->position += 1;
                    } else {
                        $_success = false;
                    }
                }

                if ($_success) {
                    $_value114[] = $this->value;

                    $this->value = $_value114;
                }

                if (!$_success) {
                    break;
                }

                $_value116[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position115;
                $this->value = $_value116;
            }

            $this->cut = $_cut117;
        }

        if ($_success) {
            $_value118[] = $this->value;

            if (substr($this->string, $this->position, strlen("*/")) === "*/") {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen("*/"));
                $this->position += strlen("*/");
            } else {
                $_success = false;

                $this->report($this->position, '"*/"');
            }
        }

        if ($_success) {
            $_value118[] = $this->value;

            $this->value = $_value118;
        }

        $this->cache['BlockComment'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'BlockComment');
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

        $_value122 = array();

        if (substr($this->string, $this->position, strlen("//")) === "//") {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen("//"));
            $this->position += strlen("//");
        } else {
            $_success = false;

            $this->report($this->position, '"//"');
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_success = true;
            $this->value = null;

            $this->cut = true;
        }

        if ($_success) {
            $_value122[] = $this->value;

            $_value120 = array();
            $_cut121 = $this->cut;

            while (true) {
                $_position119 = $this->position;

                $this->cut = false;
                if (preg_match('/^[^\\r\\n]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    break;
                }

                $_value120[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position119;
                $this->value = $_value120;
            }

            $this->cut = $_cut121;
        }

        if ($_success) {
            $_value122[] = $this->value;

            $this->value = $_value122;
        }

        $this->cache['InlineComment'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'InlineComment');
        }

        return $_success;
    }

    private function line()
    {
        return count(explode("\n", substr($this->string, 0, $this->position)));
    }

    private function rest()
    {
        return '"' . substr($this->string, $this->position) . '"';
    }

    protected function report($position, $expecting)
    {
        if ($this->cut && !isset($this->errors[$position])) {
            $this->errors[$position] = $expecting;
        }

        if (!$this->cut) {
            $this->warnings[$position][] = $expecting;
        }
    }

    private function expecting()
    {
        if (!empty($this->errors)) {
            ksort($this->errors);

            return end($this->errors);
        }

        ksort($this->warnings);

        return implode(', ', end($this->warnings));
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