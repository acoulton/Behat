<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Definition\Call;

use Behat\Testwork\Call\RuntimeCallee;

/**
 * When steps definition.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @phpstan-import-type TBehatCallable from RuntimeCallee
 */
final class When extends RuntimeDefinition
{
    public const KEYWORD = 'When';

    /**
     * Initializes definition.
     *
     * @phpstan-param TBehatCallable $callable
     */
    public function __construct(string $pattern, callable|array $callable, ?string $description = null)
    {
        parent::__construct(self::KEYWORD, $pattern, $callable, $description);
    }
}
