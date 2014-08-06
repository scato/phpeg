Feature: Generating parsers
  In order to parse a grammar
  As a developer
  I want to generate a parser

  Scenario: I have a grammar that I want to convert to a parser
    Given I have a grammar containing:
    """
    grammar Test
    {
      start Foo = "bar";
    }
    """
    When I run "generate"
    Then my class should contain:
    """
    <?php
    """
    And my class should contain:
    """
    class Test
    """
    And my class should contain:
    """
    protected function parseFoo()
    """

