<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Snippet\Printer;

use Behat\Behat\Definition\Translator\TranslatorInterface;
use Behat\Behat\Snippet\AggregateSnippet;
use Behat\Gherkin\Node\StepNode;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

use function count;

/**
 * Behat console-based snippet printer.
 *
 * Extends default printer with default styles.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ConsoleSnippetPrinter implements SnippetPrinter
{
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Initializes printer.
     */
    public function __construct(OutputInterface $output, TranslatorInterface $translator)
    {
        $this->output = $output;
        $this->translator = $translator;

        $output->getFormatter()->setStyle('snippet_keyword', new OutputFormatterStyle(null, null, ['bold']));
        $output->getFormatter()->setStyle('snippet_undefined', new OutputFormatterStyle('yellow'));
    }

    /**
     * Prints snippets of specific target.
     *
     * @param string             $targetName
     * @param AggregateSnippet[] $snippets
     */
    public function printSnippets($targetName, array $snippets)
    {
        $message = $this->translator->trans('snippet_proposal_title', ['%count%' => $targetName], 'output');

        $this->output->writeln('--- ' . $message . PHP_EOL);

        $usedClasses = [];
        foreach ($snippets as $snippet) {
            foreach ($snippet->getUsedClasses() as $usedClass) {
                $usedClasses[$usedClass] = true;
            }

            $this->output->writeln(sprintf('<snippet_undefined>%s</snippet_undefined>', $snippet->getSnippet()) . PHP_EOL);
        }

        $this->outputClassesUsesStatements(array_keys($usedClasses));
    }

    /**
     * Prints undefined steps of specific suite.
     *
     * @param string     $suiteName
     * @param StepNode[] $steps
     */
    public function printUndefinedSteps($suiteName, array $steps)
    {
        $message = $this->translator->trans('snippet_missing_title', ['%count%' => $suiteName], 'output');

        $this->output->writeln('--- ' . $message . PHP_EOL);

        foreach ($steps as $step) {
            $this->output->writeln(sprintf('    <snippet_undefined>%s %s</snippet_undefined>', $step->getKeyword(), $step->getText()));
        }

        $this->output->writeln('');
    }

    /**
     * @param array<string> $usedClasses
     */
    public function outputClassesUsesStatements(array $usedClasses): void
    {
        if ([] === $usedClasses) {
            return;
        }

        $message = $this->translator->trans('snippet_proposal_use', ['%count%' => count($usedClasses)], 'output');

        $this->output->writeln('--- ' . $message . PHP_EOL);

        foreach ($usedClasses as $usedClass) {
            $this->output->writeln(sprintf('    <snippet_undefined>use %s;</snippet_undefined>', $usedClass));
        }
    }
}
