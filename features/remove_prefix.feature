Feature: Remove prefix
  In order to have cleaner output with shorter paths
  As a developer
  I need to be able to ask Behat to remove specific prefixes from paths in the output

  Background:
    Given I initialise the working directory from the "RemovePrefix" fixtures folder
    And I provide the following options for all behat invocations:
      | option      | value |
      | --no-colors |       |

  Scenario: Add option in command line
    When I run behat with the following additional options:
      | option          | value                         |
      | --remove-prefix | features/bootstrap/,features/ |
    Then the output should contain:
      """
        Scenario:                                    # test.feature:3
          Given I have a passing step                # FeatureContext::iHaveAPassingStep()
          And I have a step that throws an exception # FeatureContext::iHaveAFailingStep()
            Warning: Undefined variable $b in FeatureContext.php line 16

      --- Failed scenarios:

          test.feature:3
      """

  Scenario: Add option in config file
    When I run behat with the following additional options:
      | option    | value         |
      | --profile | remove_prefix |
    Then the output should contain:
      """
        Scenario:                                    # test.feature:3
          Given I have a passing step                # FeatureContext::iHaveAPassingStep()
          And I have a step that throws an exception # FeatureContext::iHaveAFailingStep()
            Warning: Undefined variable $b in FeatureContext.php line 16

      --- Failed scenarios:

          test.feature:3
      """

  Scenario: Use remove prefix with editor URL
    When I run behat with the following additional options:
      | option          | value                                           |
      | --editor-url    | 'phpstorm://open?file={relPath}&line={line}'    |
      | --remove-prefix | {{PATH:features/bootstrap/}},{{PATH:features/}} |
    Then the output should contain:
      """
        Scenario:                                    # <href=phpstorm://open?file={{PATH:features/test.feature}}&line=3>{{PATH:test.feature}}:3</>
          Given I have a passing step                # FeatureContext::iHaveAPassingStep()
          And I have a step that throws an exception # FeatureContext::iHaveAFailingStep()
            Warning: Undefined variable $b in <href=phpstorm://open?file={{PATH:features/bootstrap/FeatureContext.php}}&line=16>FeatureContext.php line 16</>

      --- Failed scenarios:

          <href=phpstorm://open?file={{PATH:features/test.feature}}&line=3>{{PATH:test.feature}}:3</>
      """

  Scenario: Use remove prefix with absolute paths
    When I run behat with the following additional options:
      | option                 | value          |
      | --print-absolute-paths |                |
      | --remove-prefix        | {{PATH:$CWD/}} |
    Then the output should contain:
      """
        Scenario:                                    # {{PATH:features/test.feature}}:3
          Given I have a passing step                # FeatureContext::iHaveAPassingStep()
          And I have a step that throws an exception # FeatureContext::iHaveAFailingStep()
            Warning: Undefined variable $b in {{PATH:features/bootstrap/FeatureContext.php}} line 16

      --- Failed scenarios:

          {{PATH:features/test.feature}}:3
      """
