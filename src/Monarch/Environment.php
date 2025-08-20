<?php

/**
 * @package Monark
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Monarch;

interface Environment
{
    public string $name { get; }
    public EnvironmentMode $mode { get; }
}
