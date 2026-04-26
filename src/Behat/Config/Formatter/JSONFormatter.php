<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Behat\Config\Formatter;

/**
 * @api
 */
final class JSONFormatter extends Formatter
{
    public const NAME = 'json';

    private const TIMER_SETTING = 'timer';

    /**
     * @api
     *
     * @param bool $timer include run time attributes in generated report
     */
    public function __construct(
        bool $timer = true,
        mixed ...$baseOptions,
    ) {
        $settings = [
            self::TIMER_SETTING => $timer,
        ];

        $settings = [...$settings, ...$baseOptions];

        parent::__construct(name: self::NAME, settings: $settings);
    }

    public static function defaults(): array
    {
        return (new self())->toArray();
    }
}
