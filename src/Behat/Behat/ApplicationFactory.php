<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Behat\Definition\ServiceContainer\DefinitionExtension;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Behat\Gherkin\ServiceContainer\GherkinExtension;
use Behat\Behat\HelperContainer\ServiceContainer\HelperContainerExtension;
use Behat\Behat\Hook\ServiceContainer\HookExtension;
use Behat\Behat\Output\ServiceContainer\Formatter\JUnitFormatterFactory;
use Behat\Behat\Output\ServiceContainer\Formatter\PrettyFormatterFactory;
use Behat\Behat\Output\ServiceContainer\Formatter\ProgressFormatterFactory;
use Behat\Behat\Snippet\ServiceContainer\SnippetExtension;
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Behat\Transformation\ServiceContainer\TransformationExtension;
use Behat\Behat\Translator\ServiceContainer\GherkinTranslationsExtension;
use Behat\Testwork\ApplicationFactory as BaseFactory;
use Behat\Testwork\Argument\ServiceContainer\ArgumentExtension;
use Behat\Testwork\Autoloader\ServiceContainer\AutoloaderExtension;
use Behat\Testwork\Call\ServiceContainer\CallExtension;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;
use Behat\Testwork\Exception\ServiceContainer\ExceptionExtension;
use Behat\Testwork\Filesystem\ServiceContainer\FilesystemExtension;
use Behat\Testwork\Ordering\ServiceContainer\OrderingExtension;
use Behat\Testwork\Output\ServiceContainer\Formatter\FormatterFactory;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\PathOptions\ServiceContainer\PathOptionsExtension;
use Behat\Testwork\ServiceContainer\ServiceProcessor;
use Behat\Testwork\Specification\ServiceContainer\SpecificationExtension;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use Behat\Testwork\Translator\ServiceContainer\TranslatorExtension;
use Composer\InstalledVersions;

/**
 * Defines the way behat is created.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class ApplicationFactory extends BaseFactory
{
    /**
     * @deprecated this constant will not be updated for releases after 3.13.0 and will be removed in the next major.
     * You can use composer's runtime API to get the behat version if you need it - see getVersion() in this class for
     * an example. Note that composer's versions will not always be simple numeric values.
     */
    public const VERSION = '3.13.0';

    protected function getName()
    {
        return 'behat';
    }

    protected function getVersion()
    {
        // Get the currently installed behat version from composer's runtime API
        return InstalledVersions::getVersion('behat/behat');
    }

    protected function getDefaultExtensions()
    {
        $processor = new ServiceProcessor();

        return [
            new ArgumentExtension(),
            new AutoloaderExtension(['' => '%paths.base%/features/bootstrap']),
            new SuiteExtension($processor),
            new OutputExtension('pretty', $this->getDefaultFormatterFactories($processor), $processor),
            new PathOptionsExtension(),
            new ExceptionExtension($processor),
            new GherkinExtension($processor),
            new CallExtension($processor),
            new TranslatorExtension(),
            new GherkinTranslationsExtension(),
            new TesterExtension($processor),
            new CliExtension($processor),
            new EnvironmentExtension($processor),
            new SpecificationExtension($processor),
            new FilesystemExtension(),
            new ContextExtension($processor),
            new SnippetExtension($processor),
            new DefinitionExtension($processor),
            new EventDispatcherExtension($processor),
            new HookExtension(),
            new TransformationExtension($processor),
            new OrderingExtension($processor),
            new HelperContainerExtension($processor),
        ];
    }

    protected function getEnvironmentVariableName()
    {
        return 'BEHAT_PARAMS';
    }

    protected function getConfigPath()
    {
        $cwd = rtrim(getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $configDir = $cwd . 'config' . DIRECTORY_SEPARATOR;
        $paths = [
            $cwd . 'behat.yaml',
            $cwd . 'behat.yml',
            $cwd . 'behat.yaml.dist',
            $cwd . 'behat.yml.dist',
            $cwd . 'behat.dist.yaml',
            $cwd . 'behat.dist.yml',
            $cwd . 'behat.php',
            $cwd . 'behat.dist.php',
            $configDir . 'behat.yaml',
            $configDir . 'behat.yml',
            $configDir . 'behat.yaml.dist',
            $configDir . 'behat.yml.dist',
            $configDir . 'behat.dist.yaml',
            $configDir . 'behat.dist.yml',
            $configDir . 'behat.php',
            $configDir . 'behat.dist.php',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Returns default formatter factories.
     *
     * @return FormatterFactory[]
     */
    private function getDefaultFormatterFactories(ServiceProcessor $processor)
    {
        return [
            new PrettyFormatterFactory($processor),
            new ProgressFormatterFactory($processor),
            new JUnitFormatterFactory(),
        ];
    }
}
