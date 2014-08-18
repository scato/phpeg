Feature: Parser errors
  In order to let others fix their syntax
  As a developer
  I need my parser to give error messages

  Scenario: A grammar without a cut operator gives us some hints
    Given I have a grammar containing:
    """
    grammar SomeErrorTest
    {
      start Test = $("(" "1" (_ "+" _ "1")* ")");
      _ = " "*;
    }
    """

    When I parse "(1 + 1 + 1)"
    Then I get "(1 + 1 + 1)"

    When I parse "(1 + 1 + )"
    Then I get the error "Syntax error, expecting ' ', '1' on line 1"

  Scenario: A grammar with a cut operator gives us useful hints
    Given I have a grammar containing:
    """
    grammar UsefulErrorTest
    {
      start Test = $("(" "1" (_ "+" ^ _ "1")* ")");
      _ = " "*;
    }
    """

    When I parse "(1 + 1 + 1)"
    Then I get "(1 + 1 + 1)"

    When I parse "(1 + 1 + )"
    Then I get the error "Syntax error, expecting '1' on line 1"
