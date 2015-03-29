Feature: Parser errors
  In order to let others fix their syntax
  As a developer
  I need my parser to give error messages

  Scenario: A grammar without a cut operator gives us some hints
    Given I have a grammar containing:
    """
    grammar SomeErrorTest
    {
      start Test = "(" "1" (_ "+" _ "1")* ")";
      _ = " "*;
    }
    """

    When I parse "(1 + 1 + 1)"
    Then I get no errors

    When I parse "(1 + 1 + )"
    Then I get the error "Syntax error, expecting " ", "1" on line 1"

  Scenario: A grammar with a cut operator gives us useful hints
    Given I have a grammar containing:
    """
    grammar UsefulErrorTest
    {
      start Test = "(" ^ "1" (_ "+" ^ _ "1")* ")";
      _ = " "*;
    }
    """

    When I parse "(1 + 1 + 1)"
    Then I get no errors

    When I parse "(1 + 1 + )"
    Then I get the error "Syntax error, expecting "1" on line 1"

    When I parse ""
    Then I get the error "Syntax error, expecting "(", Test on line 1"

  Scenario: A grammar with named rules gives us custom hints
    Given I have a grammar containing:
    """
    grammar NamedRuleTest
    {
      start Test "expression" = "(" ^ "1" (_ "+" ^ _ "1")* ")";
      _ = " "*;
    }
    """

    When I parse ""
    Then I get the error "Syntax error, expecting "(", expression on line 1"

  Scenario: A grammar which accepts part of the file gives us useful line numbers
    Given I have a grammar containing:
    """
    grammar LineNumberTest
    {
        start Test = Operation*;
        Operation = "ADD" " " Register ", " Register ", " Register "\n"
                  / "PUSH" " " Register ", " Value "\n"
                  / "MOV" " " Register ", " Register "\n"
                  / "DO" "\n" Operation* "LOOP" "\n";
        Register = [a-z];
        Value = [0-9];
    }
    """

    When I parse
    """
    PUSH a, 1
    PUSH b, 1
    DO
    ADD a, b, c
    MOV c, a
    LOOP

    """
    Then I get no errors

    When I parse
    """
    PUSH a, 1
    PUSH b, 1
    DO
    ADD a, b, c
    MOV c, a

    """
    Then I get the error "Syntax error, expecting "ADD", "PUSH", "MOV", "DO", Operation, "LOOP" on line 6"
