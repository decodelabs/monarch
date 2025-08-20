<?php

/**
 * @package Monarch
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Monarch;

interface Build
{
    public bool $compiled { get; }
    public string $path { get; }
    public ?int $time { get; }
    public int $cacheBuster { get; }
}
