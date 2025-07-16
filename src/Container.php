<?php

/**
 * @package Monarch
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Monarch;

use DecodeLabs\Exceptional;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface as NotFoundException;

class Container implements ContainerInterface
{
    /**
     * @var array<string,object>
     */
    protected array $items = [];

    /**
     * @template T of object
     * @param class-string<T> $id
     * @param T $value
     */
    public function set(
        string $id,
        object $value
    ): void {
        $this->items[$id] = $value;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get(
        string $id
    ): mixed {
        if (isset($this->items[$id])) {
            /** @var T $output */
            $output = $this->items[$id];
            return $output;
        }

        throw Exceptional::NotFound(
            message: $id . ' has not been bound',
            interfaces: [NotFoundException::class]
        );
    }

    public function has(
        string $id
    ): bool {
        return isset($this->items[$id]);
    }
}
