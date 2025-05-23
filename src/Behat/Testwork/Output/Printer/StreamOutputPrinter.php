<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Testwork\Output\Printer;

use Behat\Testwork\Output\Printer\Factory\OutputFactory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class StreamOutputPrinter implements OutputPrinter
{
    private ?OutputInterface $output = null;

    public function __construct(
        private OutputFactory $outputFactory,
    ) {
    }

    /**
     * @return OutputFactory
     */
    protected function getOutputFactory()
    {
        return $this->outputFactory;
    }

    public function setOutputPath($path)
    {
        $this->outputFactory->setOutputPath($path);
        $this->flush();
    }

    public function getOutputPath()
    {
        return $this->outputFactory->getOutputPath();
    }

    public function setOutputStyles(array $styles)
    {
        $this->outputFactory->setOutputStyles($styles);
        $this->flush();
    }

    public function getOutputStyles()
    {
        return $this->outputFactory->getOutputStyles();
    }

    public function setOutputDecorated($decorated)
    {
        $this->outputFactory->setOutputDecorated($decorated);
        $this->flush();
    }

    public function isOutputDecorated()
    {
        return $this->outputFactory->isOutputDecorated();
    }

    public function setOutputVerbosity($level)
    {
        $this->outputFactory->setOutputVerbosity($level);
        $this->flush();
    }

    public function getOutputVerbosity()
    {
        return $this->outputFactory->getOutputVerbosity();
    }

    public function write($messages)
    {
        $this->getWritingStream()->write($messages, false);
    }

    public function writeln($messages = '')
    {
        $this->getWritingStream()->write($messages, true);
    }

    public function flush()
    {
        $this->output = null;
    }

    /**
     * Returns output instance, prepared to write.
     *
     * @return OutputInterface
     */
    final protected function getWritingStream()
    {
        if (null === $this->output) {
            $this->output = $this->outputFactory->createOutput();
        }

        return $this->output;
    }
}
