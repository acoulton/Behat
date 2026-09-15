Feature: Mixed failures

  @failing-before-scenario-hook
  Scenario: skipped by the failing before-scenario hook
    Given a step that passes

  Scenario: fails at step level
    Given a step that fails

  Scenario: everything passes
    Given a step that passes
