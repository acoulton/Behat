Feature: Before scenario hooks

  @failing-before-scenario-hook
  Scenario: the before-scenario hook throws
    Given a step that passes

  Scenario: everything passes
    Given a step that passes
