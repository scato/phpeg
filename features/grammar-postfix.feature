Feature: Defining grammars (postfix)
  In order to define a grammar
  As a developer
  I need repetition operators to do so

  Scenario: I parse repetitions using a star
    Given I have a grammar containing:
    """
    grammar ZeroOrMoreTest
    {
      start Test = "f" "o"*;
    }
    """

    When I parse "f"
    Then I get ["f", []]

    When I parse "fo"
    Then I get ["f", ["o"]]

    When I parse "foo"
    Then I get ["f", ["o", "o"]]

    When I parse "for"
    Then I get an error

  Scenario: I parse repetitions using a star and a trailing literal
    Given I have a grammar containing:
    """
    grammar ZeroOrMoreTest2
    {
      start Test = "f" "o"* "r";
    }
    """
    When I parse "foor"
    Then I get ["f", ["o", "o"], "r"]

  Scenario: I parse repetitions using a plus
    Given I have a grammar containing:
    """
    grammar OneOrMoreTest
    {
      start Test = "f" "o"+;
    }
    """

    When I parse "f"
    Then I get an error

    When I parse "fo"
    Then I get ["f", ["o"]]

    When I parse "foo"
    Then I get ["f", ["o", "o"]]

    When I parse "for"
    Then I get an error

  Scenario: I parse repetitions using a plus and a trailing literal
    Given I have a grammar containing:
    """
    grammar OneOrMoreTest2
    {
      start Test = "f" "o"+ "r";
    }
    """
    When I parse "foor"
    Then I get ["f", ["o", "o"], "r"]

  Scenario: I parse repetitions using a question mark
    Given I have a grammar containing:
    """
    grammar OptionalTest
    {
      start Test = "f" "o"?;
    }
    """

    When I parse "f"
    Then I get ["f", null]

    When I parse "fo"
    Then I get ["f", "o"]

    When I parse "foo"
    Then I get an error

    When I parse "for"
    Then I get an error

  Scenario: I parse repetitions using a question mark and a trailing literal
    Given I have a grammar containing:
    """
    grammar OptionalTest2
    {
      start Test = "f" "o"? "r";
    }
    """
    When I parse "for"
    Then I get ["f", "o", "r"]
