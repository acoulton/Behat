<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\EventDispatcher\Event;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface;

/**
 * Represents an event of scenario-like structure (Scenario, Background, Example).
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @deprecated This will be removed in 4.0, in favour of separate inheritance chains for `ScenarioTested` and `BackgroundTested` events
 */
interface ScenarioLikeTested extends GherkinNodeTested
{
    /**
     * Returns feature node.
     */
    public function getFeature(): FeatureNode;

    /**
     * Returns scenario node.
     */
    public function getScenario(): ScenarioLikeInterface;
}
