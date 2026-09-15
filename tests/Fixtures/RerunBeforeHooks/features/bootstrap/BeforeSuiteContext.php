<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Hook\BeforeSuite;

class BeforeSuiteContext implements Context
{
    #[BeforeSuite]
    public static function failBeforeSuite(): void
    {
        throw new RuntimeException('before suite hook failure');
    }
}
