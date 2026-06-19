<?php

/*
 * This file is part of the Behat Testwork.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Testwork\Call;

use Behat\Behat\Context\Context;
use Behat\Testwork\Call\Exception\BadCallbackException;
use Behat\Testwork\Deprecation\DeprecationCollector;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * Represents callee created and executed in the runtime.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @phpstan-type TBehatCallable callable|array{class-string<Context>, string}
 */
class RuntimeCallee implements Callee
{
    /**
     * @var TBehatCallable
     */
    private $callable;
    private ReflectionMethod|ReflectionFunction $reflection;
    private string $path;

    /**
     * Initializes callee.
     *
     * @phpstan-param TBehatCallable $callable
     */
    public function __construct(
        callable|array $callable,
        private readonly ?string $description = null,
    ) {
        if (is_array($callable)) {
            if (!$this->isCallableOrBehatContextCallable($callable)) {
                DeprecationCollector::trigger('Creating '.static::class.' with a non-callable array other than a Behat context method reference is deprecated');
            }
            $this->reflection = new ReflectionMethod($callable[0], $callable[1]);
            $this->path = $callable[0] . '::' . $callable[1] . '()';
        } else {
            $this->reflection = new ReflectionFunction($callable);
            $this->path = $this->reflection->getFileName() . ':' . $this->reflection->getStartLine();
        }

        $this->callable = $callable;
    }

    private function isCallableOrBehatContextCallable(array $callable): bool
    {
        if (is_callable($callable)) {
            return true;
        }

        return count($callable) === 2
            && is_string($callable[0])
            && is_string($callable[1])
            && is_a($callable[0], Context::class, true);
    }

    /**
     * Returns callee description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns callee definition path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns callable.
     *
     * @return TBehatCallable
     */
    public function getCallable(): callable|array
    {
        return $this->callable;
    }

    /**
     * Returns callable reflection.
     */
    public function getReflection(): ReflectionFunctionAbstract
    {
        return $this->reflection;
    }

    /**
     * Returns true if callee is a method, false otherwise.
     */
    public function isAMethod(): bool
    {
        return $this->reflection instanceof ReflectionMethod;
    }

    /**
     * Returns true if callee is an instance (non-static) method, false otherwise.
     */
    public function isAnInstanceMethod(): bool
    {
        return $this->reflection instanceof ReflectionMethod
            && !$this->reflection->isStatic();
    }

    final protected function throwIfCallableIsInstanceMethod(string $hookType): void
    {
        if ($this->isAnInstanceMethod()) {
            throw new BadCallbackException(sprintf(
                '%s hook callback: %s must be a static method',
                $hookType,
                $this->getPath(),
            ), $this->getCallable());
        }
    }
}
