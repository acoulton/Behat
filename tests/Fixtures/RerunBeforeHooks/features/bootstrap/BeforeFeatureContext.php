<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Hook\BeforeFeature;

class BeforeFeatureContext implements Context
{
    #[BeforeFeature('@failing-before-feature-hook')]
    public static function failBeforeFeature(): void
    {
        throw new RuntimeException('before feature hook failure');
    }
}
