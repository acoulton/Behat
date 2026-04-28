<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\EventDispatcher\Event;

use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\NodeInterface;
use Behat\Testwork\EventDispatcher\Event\LifecycleEvent;

/**
 * Represents a background event.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @api
 */
abstract class BackgroundTested extends LifecycleEvent implements GherkinNodeTested
{
    public const BEFORE = 'tester.background_tested.before';
    public const AFTER_SETUP = 'tester.background_tested.after_setup';
    public const BEFORE_TEARDOWN = 'tester.background_tested.before_teardown';
    public const AFTER = 'tester.background_tested.after';

    /**
     * Returns feature node.
     */
    abstract public function getFeature(): FeatureNode;

    /**
     * Returns background node.
     */
    abstract public function getBackground(): BackgroundNode;

    /**
     * Returns node.
     */
    final public function getNode(): NodeInterface
    {
        return $this->getBackground();
    }
}
