<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\HelperContainer\Argument;

use Behat\Behat\Context\Argument\ArgumentResolver;
use Behat\Behat\Context\Argument\ArgumentResolverFactory;
use Behat\Behat\HelperContainer\BuiltInServiceContainer;
use Behat\Behat\HelperContainer\Environment\ServiceContainerEnvironment;
use Behat\Behat\HelperContainer\Exception\WrongContainerClassException;
use Behat\Behat\HelperContainer\Exception\WrongServicesConfigurationException;
use Behat\Behat\HelperContainer\ServiceContainer\HelperContainerExtension;
use Behat\Testwork\Environment\Environment;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;

/**
 * Generates ServiceContainer argument resolvers based on suite's `services` setting.
 *
 * @see ContextEnvironmentHandler
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class ServicesResolverFactory implements ArgumentResolverFactory
{
    /**
     * Initialises factory.
     */
    public function __construct(
        private readonly TaggedContainerInterface $container,
    ) {
    }

    /**
     * @throws WrongServicesConfigurationException
     * @throws WrongContainerClassException
     */
    public function createArgumentResolvers(Environment $environment): array
    {
        $suite = $environment->getSuite();

        if (!$suite->hasSetting('services')) {
            return [];
        }

        $container = $this->createContainer($suite->getSetting('services'));
        $autowire = $suite->hasSetting('autowire') && $suite->getSetting('autowire');

        if ($environment instanceof ServiceContainerEnvironment) {
            $environment->setServiceContainer($container);
        }

        return $this->createResolvers($container, $autowire);
    }

    /**
     * Creates container from the setting passed.
     *
     * @throws WrongServicesConfigurationException
     */
    private function createContainer(mixed $settings): ContainerInterface
    {
        if (is_string($settings)) {
            return $this->createContainerFromString($settings);
        }

        if (is_array($settings)) {
            return $this->createContainerFromArray($settings);
        }

        throw new WrongServicesConfigurationException(
            sprintf('`services` must be either string or an array, but `%s` given.', gettype($settings))
        );
    }

    /**
     * Creates custom container using class/constructor given.
     *
     * @throws WrongServicesConfigurationException
     */
    private function createContainerFromString(string $settings): ContainerInterface
    {
        if (0 === mb_strpos($settings, '@')) {
            return $this->loadContainerFromContainer(mb_substr($settings, 1));
        }

        return $this->createContainerFromClassSpec($settings);
    }

    /**
     * Creates built-in service container with provided settings.
     */
    private function createContainerFromArray(array $settings): BuiltInServiceContainer
    {
        return new BuiltInServiceContainer($settings);
    }

    /**
     * Loads container from string.
     *
     * @throws WrongServicesConfigurationException
     */
    private function loadContainerFromContainer(string $name): ContainerInterface
    {
        $services = $this->container->findTaggedServiceIds(HelperContainerExtension::HELPER_CONTAINER_TAG);

        if (!array_key_exists($name, $services)) {
            throw new WrongServicesConfigurationException(
                sprintf('Service container `@%s` was not found.', $name)
            );
        }

        return $this->ensureValidContainer(
            $this->container->get($name),
            sprintf('service `%s`', $name)
        );
    }

    /**
     * Creates container from string-based class spec.
     */
    private function createContainerFromClassSpec(string $classSpec): ContainerInterface
    {
        $constructor = explode('::', $classSpec);

        if (2 === count($constructor)) {
            return $this->ensureValidContainer(
                call_user_func($constructor),
                $classSpec
            );
        }

        return $this->ensureValidContainer(
            new $constructor[0](),
            $classSpec
        );
    }

    /**
     * Checks if container implements the correct interface and creates resolver using it.
     *
     * @return list<ArgumentResolver>
     *
     * @throws WrongContainerClassException
     */
    private function createResolvers(ContainerInterface $container, bool $autowire): array
    {
        if ($autowire) {
            return [new ServicesResolver($container), new AutowiringResolver($container)];
        }

        return [new ServicesResolver($container)];
    }

    private function ensureValidContainer(mixed $container, string $source): ContainerInterface
    {
        if ($container instanceof ContainerInterface) {
            return $container;
        }

        throw new WrongContainerClassException(
            sprintf(
                'Expected `%s` to provide an implementation of %s, but it does not (got %s)',
                $source, ContainerInterface::class, get_debug_type($container)
            ),
            is_object($container) ? $container::class : null
        );
    }
}
