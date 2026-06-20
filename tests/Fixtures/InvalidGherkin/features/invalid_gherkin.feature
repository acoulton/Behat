Feature: Invalid gherkin
  In order to have a good developer experience
  As a tester
  I need to get reports about invalid feature files

  Scenario: Reports
      Given I have a feature file with invalid content
      When  I run Behat
      Then  it should report the errors about the incomplete table:
        | foo |
        | bar
