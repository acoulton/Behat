Feature: Invalid Gherkin
  In order to have a good developer experience
  As a tester
  I need to get reports about invalid feature files

  Background:
    Given I initialise the working directory from the InvalidGherkin fixtures folder
    And   I provide the following options for all behat invocations:
      | option      | value    |
      | --no-colors |          |
      | --format    | progress |

  Scenario: Reports
    When I run "behat"
    Then it should fail with:
      """
      In Parser.php line XX:

        Table row '1' is expected to have 1 columns, got 0 in file %%WORKING_DIR%%features%%DS%%invalid_gherkin.feature


      In TableNode.php line XX:

        Table row '1' is expected to have 1 columns, got 0
      """
