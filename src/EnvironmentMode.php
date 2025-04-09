<?php

/**
 * @package Monark
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Monarch;

use DecodeLabs\Enumerable\Backed\ValueString;
use DecodeLabs\Enumerable\Backed\ValueStringTrait;

enum EnvironmentMode: string implements ValueString
{
    use ValueStringTrait;

    case Development = 'development';
    case Testing = 'testing';
    case Production = 'production';

    public function isDevelopment(): bool
    {
        return $this === self::Development;
    }

    public function isTesting(): bool
    {
        return
            $this === self::Development ||
            $this === self::Testing;
    }

    public function isProduction(): bool
    {
        return $this === self::Production;
    }
}
