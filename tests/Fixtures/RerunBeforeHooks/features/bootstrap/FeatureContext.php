<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;

class FeatureContext implements Context
{
    #[Given('a step that passes')]
    public function aStepThatPasses(): void
    {
    }

    #[Given('a step that fails')]
    public function aStepThatFails(): void
    {
        throw new RuntimeException('step failure');
    }

    #[BeforeScenario('@failing-before-scenario-hook')]
    public static function failBeforeScenario(): void
    {
        throw new RuntimeException('before scenario hook failure');
    }
}
