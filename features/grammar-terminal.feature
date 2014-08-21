Feature: Defining grammars (terminal)
  In order to define a grammar
  As a developer
  I need terminal expressions to do so

  Scenario: I parse a literal string
    Given I have a grammar containing:
    """
    grammar LiteralTest
    {
      start Test = "foo";
    }
    """

    When I parse "foo"
    Then I get "foo"

    When I parse "foobar"
    Then I get an error

  Scenario: I parse an XML tag using a back-reference
    Given I have a grammar containing:
    """
    grammar LiteralBackReferenceTest
    {
      start Test =
        "<" tagName:$([a-z\-]*) ">"
        $([^<]*)
        "</{$tagName}>";
    }
    """

    When I parse "<a>foo</a>"
    Then I get ["<", "a", ">", "foo", "</a>"]

    When I parse "<a>foo</b>"
    Then I get an error

  Scenario: I parse a literal string using single quotes
    Given I have a grammar containing:
    """
    grammar SingleQuoteLiteralTest
    {
      start Test = 'foo';
    }
    """

    When I parse "foo"
    Then I get "foo"

    When I parse "foobar"
    Then I get an error

  Scenario: I try to parse an XML tag using a back-reference and single quotes
    Given I have a grammar containing:
    """
    grammar SingleQuotedLiteralBackReferenceTest
    {
      start Test =
        '<' tagName:$([a-z\-]*) '>'
        $([^<]*)
        '</{$tagName}>';
    }
    """

    When I parse "<a>foo</a>"
    Then I get an error

    When I parse "<a>foo</{$tagName}>"
    Then I get ["<", "a", ">", "foo", "</{$tagName}>"]

  Scenario: I parse the PHP empty keyword (which is case insensitive)
    Given I have a grammar containing:
    """
    grammar CaseInsensitiveLiteralTest
    {
      start Test = 'empty'i;
    }
    """

    When I parse "empty"
    Then I get "empty"

    When I parse "Empty"
    Then I get "Empty"

    When I parse "null"
    Then I get an error

  Scenario: I parse any character
    Given I have a grammar containing:
    """
    grammar AnyTest
    {
      start Test = . "oo";
    }
    """

    When I parse "boo"
    Then I get ["b", "oo"]

    When I parse "oo"
    Then I get an error

  Scenario: I parse something using a character class
    Given I have a grammar containing:
    """
    grammar CharacterClassTest
    {
      start Test = [0-9a-f] "oo";
    }
    """

    When I parse "foo"
    Then I get ["f", "oo"]

    When I parse "zoo"
    Then I get an error

  Scenario: I parse something using a complement character class
    Given I have a grammar containing:
    """
    grammar CharacterClassTest2
    {
      start Test = [^0-9a-f] "oo";
    }
    """
    When I parse "foo"
    Then I get an error

  Scenario: I parse something using rules
    Given I have a grammar containing:
    """
    grammar RuleTest
    {
      start Test = Hex NoHex NoHex;
      Hex = [0-9a-f];
      NoHex = [^0-9a-f];
    }
    """

    When I parse "foo"
    Then I get ["f", "o", "o"]

    When I parse "bar"
    Then I get an error

  Scenario: I parse something using a sub-expression
    Given I have a grammar containing:
    """
    grammar SubExpressionTest
    {
      start Test = ("f" "o")*;
    }
    """

    When I parse "foo"
    Then I get an error

    When I parse "fofo"
    Then I get [["f", "o"], ["f", "o"]]
