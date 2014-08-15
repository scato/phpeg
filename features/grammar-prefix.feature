Feature: Defining grammars (prefix)
  In order to define a grammar
  As a developer
  I need predicates and the matched string operator to do so

  Scenario: I parse something using an and predicate
    Given I have a grammar containing:
    """
    grammar AndPredicateTest
    {
      start Test = &"for" [a-z]+;
    }
    """

    When I parse "for"
    Then I get [null, ["f", "o", "r"]]

    When I parse "ford"
    Then I get [null, ["f", "o", "r", "d"]]

    When I parse "bard"
    Then I get an error

  Scenario: I parse something using a not predicate
    Given I have a grammar containing:
    """
    grammar NotPredicateTest
    {
      start Test = !"for" [a-z]+;
    }
    """

    When I parse "for"
    Then I get an error

    When I parse "ford"
    Then I get an error

    When I parse "bard"
    Then I get [null, ["b", "a", "r", "d"]]

  Scenario: I parse something, but I only want the string that was matched
    Given I have a grammar containing:
    """
    grammar MatchedStringTest
    {
      start Test = $(!"for" [a-z]+);
    }
    """
    When I parse "bard"
    Then I get "bard"
