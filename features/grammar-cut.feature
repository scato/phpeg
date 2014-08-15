Feature: Defining grammars (cut)
  In order to define a grammar
  As a developer
  I need the cut operator

  Scenario: I use a cut operator in combination with a choice
    Given I have a grammar containing:
    """
    grammar CutTest
    {
      start Test = "foo" ^ "bar" / .*;
    }
    """

    When I parse "foobar"
    Then I get ["foo", null, "bar"]

    When I parse "barfoo"
    Then I get ["b", "a", "r", "f", "o", "o"]

    When I parse "footer"
    Then I get an error

  Scenario: I use a cut operator in combination with a star
    Given I have a grammar containing:
    """
    grammar CutZeroOrMoreTest
    {
      start Test = ("foo" ^ "bar")* .*;
    }
    """

    When I parse "foobar"
    Then I get [[["foo", null, "bar"]], []]

    When I parse "foobarfoobar"
    Then I get [[["foo", null, "bar"], ["foo", null, "bar"]], []]

    When I parse "barfoo"
    Then I get [[], ["b", "a", "r", "f", "o", "o"]]

    When I parse "footer"
    Then I get an error

    When I parse "foobarfooter"
    Then I get an error

  Scenario: I use a cut operator in combination with a plus
    Given I have a grammar containing:
    """
    grammar CutOneOrMoreTest
    {
      start Test = ("foo" ^ "bar")+ .*;
    }
    """

    When I parse "foobar"
    Then I get [[["foo", null, "bar"]], []]

    When I parse "foobarfoobar"
    Then I get [[["foo", null, "bar"], ["foo", null, "bar"]], []]

    When I parse "foobarbarfoo"
    Then I get [[["foo", null, "bar"]], ["b", "a", "r", "f", "o", "o"]]

    When I parse "footer"
    Then I get an error

    When I parse "foobarfooter"
    Then I get an error

  Scenario: I use a cut operator in combination with a question mark
    Given I have a grammar containing:
    """
    grammar CutOptionalTest
    {
      start Test = ("foo" ^ "bar")? .*;
    }
    """

    When I parse "foobar"
    Then I get [["foo", null, "bar"], []]

    When I parse "foobarfoobar"
    Then I get [["foo", null, "bar"], ["f", "o", "o", "b", "a", "r"]]

    When I parse "barfoo"
    Then I get [null, ["b", "a", "r", "f", "o", "o"]]

    When I parse "footer"
    Then I get an error

    When I parse "foobarfooter"
    Then I get [["foo", null, "bar"], ["f", "o", "o", "t", "e", "r"]]
