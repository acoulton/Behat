<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Step;

use Attribute;

/**
 * Represents an Attribute for When steps.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class When implements Definition
{
    /**
     * @var string|null
     */
    public $pattern;

    public function __construct($pattern = null)
    {
        $this->pattern = $pattern;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }
}
