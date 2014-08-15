Feature: Defining grammars (infix)
  In order to define a grammar
  As a developer
  I need infix operators to do so

  Scenario: I parse a sequence, assigning part of it to a variable
    Given I have a grammar containing:
    """
    grammar LabelTest
    {
      start Test = "foo" part:"bar" { return $part; };
    }
    """

    When I parse "foobar"
    Then I get "bar"

  Scenario: I parse string using a sequence
    Given I have a grammar containing:
    """
    grammar SequenceTest
    {
      start Test = [a-z]+ "-" [0-9]+ "-" [a-z]+;
    }
    """

    When I parse "tk-13-ff"
    Then I get [["t", "k"], "-", ["1", "3"], "-", ["f", "f"]]

    When I parse "tk-13-24"
    Then I get an error

  Scenario: I parse a string, if it includes an optional "upper" part, the output changes to upper case
    Given I have a grammar containing:
    """
    grammar ActionTest
    {
      start Test = (upper:"upper" " "+)? string:$([a-z]+) {
        if (isset($upper)) {
          $string = strtoupper($string);
        }

        return $string;
      };
    }
    """

    When I parse "upper foobar"
    Then I get "FOOBAR"

    When I parse "foobar"
    Then I get "foobar"

  Scenario: I parse a string using a list of choices
    Given I have a grammar containing:
    """
    grammar ChoiceTest
    {
      start Test = "foo" / "bar" / [a-f0-9]+;
    }
    """

    When I parse "foo"
    Then I get "foo"

    When I parse "bar"
    Then I get "bar"

    When I parse "123abc"
    Then I get ["1", "2", "3", "a", "b", "c"]

    When I parse "123xyz"
    Then I get an error

    When I parse "BAR"
    Then I get an error
