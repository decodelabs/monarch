<?php

/**
 * Monarch
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Monarch;

use Throwable;

interface ExceptionLogger
{
    public function logException(
        Throwable $exception
    ): void;
}
