Feature: Rerun scenarios that were skipped by a failing before hook
  In order to not let a green re-run hide a failure
  As a feature developer
  I need scenarios skipped by a failing before hook to be recorded for --rerun

  Background:
    Given I initialise the working directory from the "RerunBeforeHooks" fixtures folder
    And I provide the following options for all behat invocations:
      | option      | value    |
      | --no-colors |          |
      | --format    | progress |

  Scenario: A scenario skipped by a failing before-scenario hook is re-run on its own
    Given I run "behat"
    Then it should fail with:
      """
      -.

      --- Failed hooks:

          BeforeScenario @failing-before-scenario-hook "features/scenario_hook.feature:4" # FeatureContext::failBeforeScenario()
            before scenario hook failure (RuntimeException)

      2 scenarios (1 passed, 1 skipped)
      2 steps (1 passed, 1 skipped)
      """
    When I run "behat --rerun"
    Then it should fail with:
      """
      -

      --- Failed hooks:

          BeforeScenario @failing-before-scenario-hook "features/scenario_hook.feature:4" # FeatureContext::failBeforeScenario()
            before scenario hook failure (RuntimeException)

      1 scenario (1 skipped)
      1 step (1 skipped)
      """

  Scenario: A feature skipped by a failing before-feature hook is re-run whole, and on its own
    Given I run "behat -p beforeFeature"
    Then it should fail with:
      """
      --..

      --- Failed hooks:

          BeforeFeature @failing-before-feature-hook "features/feature_hook.feature" # BeforeFeatureContext::failBeforeFeature()
            before feature hook failure (RuntimeException)

      4 scenarios (2 passed, 2 skipped)
      4 steps (2 passed, 2 skipped)
      """
    When I run "behat -p beforeFeature --rerun"
    Then it should fail with:
      """
      --

      --- Failed hooks:

          BeforeFeature @failing-before-feature-hook "features/feature_hook.feature" # BeforeFeatureContext::failBeforeFeature()
            before feature hook failure (RuntimeException)

      2 scenarios (2 skipped)
      2 steps (2 skipped)
      """

  Scenario: A suite skipped by a failing before-suite hook is re-run whole
    Given I run "behat -p beforeSuite"
    Then it should fail with:
      """
      ----

      --- Failed hooks:

          BeforeSuite "default" # BeforeSuiteContext::failBeforeSuite()
            before suite hook failure (RuntimeException)

      4 scenarios (4 skipped)
      4 steps (4 skipped)
      """
    When I run "behat -p beforeSuite --rerun"
    Then it should fail with:
      """
      ----

      --- Failed hooks:

          BeforeSuite "default" # BeforeSuiteContext::failBeforeSuite()
            before suite hook failure (RuntimeException)

      4 scenarios (4 skipped)
      4 steps (4 skipped)
      """

  Scenario: A run mixing a step failure and a hook failure re-runs both
    Given I run "behat -p mixed"
    Then it should fail with:
      """
      -F.

      --- Failed hooks:

          BeforeScenario @failing-before-scenario-hook "features/mixed.feature:4" # FeatureContext::failBeforeScenario()
            before scenario hook failure (RuntimeException)

      --- Failed steps:

      001 Scenario: fails at step level # features/mixed.feature:7
            Given a step that fails     # features/mixed.feature:8
              step failure (RuntimeException)

      3 scenarios (1 passed, 1 failed, 1 skipped)
      3 steps (1 passed, 1 failed, 1 skipped)
      """
    When I run "behat -p mixed --rerun"
    Then it should fail with:
      """
      -F

      --- Failed hooks:

          BeforeScenario @failing-before-scenario-hook "features/mixed.feature:4" # FeatureContext::failBeforeScenario()
            before scenario hook failure (RuntimeException)

      --- Failed steps:

      001 Scenario: fails at step level # features/mixed.feature:7
            Given a step that fails     # features/mixed.feature:8
              step failure (RuntimeException)

      2 scenarios (1 failed, 1 skipped)
      2 steps (1 failed, 1 skipped)
      """
