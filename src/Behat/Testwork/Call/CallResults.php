<?php

/*
 * This file is part of the Behat Testwork.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Testwork\Call;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Aggregates multiple call results into a collection and provides an informational API on top of that.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @template T of Call
 *
 * @implements IteratorAggregate<int, CallResult<T>>
 */
final class CallResults implements Countable, IteratorAggregate
{
    /**
     * Initializes call results collection.
     *
     * @param CallResult<T>[] $results
     */
    public function __construct(
        private readonly array $results = [],
    ) {
    }

    /**
     * Merges results from provided collection into the current one.
     *
     * @template U of Call
     *
     * @param CallResults<U> $first
     * @param CallResults<U> $second
     *
     * @return CallResults<U>
     */
    public static function merge(CallResults $first, CallResults $second): self
    {
        return new self(array_merge($first->toArray(), $second->toArray()));
    }

    /**
     * Checks if any call in collection throws an exception.
     */
    public function hasExceptions(): bool
    {
        foreach ($this->results as $result) {
            if ($result->hasException()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if any call in collection produces an output.
     */
    public function hasStdOuts(): bool
    {
        foreach ($this->results as $result) {
            if ($result->hasStdOut()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns amount of results.
     */
    public function count(): int
    {
        return count($this->results);
    }

    /**
     * Returns collection iterator.
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->results);
    }

    /**
     * Returns call results array.
     *
     * @return CallResult<T>[]
     */
    public function toArray(): array
    {
        return $this->results;
    }
}
