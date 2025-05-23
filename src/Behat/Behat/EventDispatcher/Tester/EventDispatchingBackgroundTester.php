<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\EventDispatcher\Tester;

use Behat\Behat\EventDispatcher\Event\AfterBackgroundSetup;
use Behat\Behat\EventDispatcher\Event\AfterBackgroundTested;
use Behat\Behat\EventDispatcher\Event\BackgroundTested;
use Behat\Behat\EventDispatcher\Event\BeforeBackgroundTeardown;
use Behat\Behat\EventDispatcher\Event\BeforeBackgroundTested;
use Behat\Behat\Tester\BackgroundTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Background tester dispatching BEFORE/AFTER events.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class EventDispatchingBackgroundTester implements BackgroundTester
{
    /**
     * @var BackgroundTester
     */
    private $baseTester;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Initializes tester.
     */
    public function __construct(BackgroundTester $baseTester, EventDispatcherInterface $eventDispatcher)
    {
        $this->baseTester = $baseTester;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setUp(Environment $env, FeatureNode $feature, $skip)
    {
        $event = new BeforeBackgroundTested($env, $feature, $feature->getBackground());

        $this->eventDispatcher->dispatch($event, $event::BEFORE);

        $setup = $this->baseTester->setUp($env, $feature, $skip);

        $event = new AfterBackgroundSetup($env, $feature, $feature->getBackground(), $setup);

        $this->eventDispatcher->dispatch($event, $event::AFTER_SETUP);

        return $setup;
    }

    public function test(Environment $env, FeatureNode $feature, $skip)
    {
        return $this->baseTester->test($env, $feature, $skip);
    }

    public function tearDown(Environment $env, FeatureNode $feature, $skip, TestResult $result)
    {
        $event = new BeforeBackgroundTeardown($env, $feature, $feature->getBackground(), $result);

        $this->eventDispatcher->dispatch($event, BackgroundTested::BEFORE_TEARDOWN);

        $teardown = $this->baseTester->tearDown($env, $feature, $skip, $result);

        $event = new AfterBackgroundTested($env, $feature, $feature->getBackground(), $result, $teardown);

        $this->eventDispatcher->dispatch($event, BackgroundTested::AFTER);

        return $teardown;
    }
}
