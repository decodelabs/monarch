<?php

/**
 * @package Monarch
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Monarch;

use DecodeLabs\Exceptional;

class Paths
{
    public string $root {
        get {
            if (isset($this->root)) {
                return $this->root;
            }

            $this->root = $this->working;
            $this->alias('@root', $this->root);
            return $this->root;
        }
        set {
            $this->root = rtrim($value, '/');
            $this->alias('@root', $this->root);
        }
    }

    public string $run {
        get {
            if (isset($this->run)) {
                return $this->run;
            }

            $this->run = $this->root;
            $this->alias('@run', $this->run);
            return $this->run;
        }
        set {
            $this->run = rtrim($value, '/');
            $this->alias('@run', $this->run);
        }
    }

    public string $working {
        get {
            if (isset($this->working)) {
                return $this->working;
            }

            if (
                isset($_SERVER['DOCUMENT_ROOT']) &&
                !empty($_SERVER['DOCUMENT_ROOT'])
            ) {
                $path = $_SERVER['DOCUMENT_ROOT'];
            } elseif (false === ($path = getcwd())) {
                throw Exceptional::Runtime(
                    'Unable to determine current working directory'
                );
            }

            /** @var string $path */
            chdir($path);
            return $this->working = rtrim($path, '/');
        }
        set {
            $this->working = rtrim($value, '/');
            chdir($value);
        }
    }

    public ?string $subjectRoot {
        set {
            if ($value !== null) {
                $value = rtrim($value, '/');
                $this->alias('@subject-root', $value);
            }

            $this->subjectRoot = $value;
        }
    }

    public string $localData {
        get {
            if (isset($this->localData)) {
                return $this->localData;
            }

            return $this->localData = $this->root . '/data/local';
        }
        set {
            $this->localData = rtrim($value, '/');
        }
    }

    public string $sharedData {
        get {
            if (isset($this->sharedData)) {
                return $this->sharedData;
            }

            return $this->sharedData = $this->root . '/data/shared';
        }
        set {
            $this->sharedData = rtrim($value, '/');
        }
    }

    /**
     * @var array<string,string>
     */
    public protected(set) array $aliases = [];

    public function alias(
        string $alias,
        string $path
    ): void {
        $alias = rtrim($alias, '/') . '/';
        $path = rtrim($path, '/') . '/';
        $this->aliases[$alias] = $this->resolve($path);
    }

    public function hasAlias(
        string $alias
    ): bool {
        $alias = rtrim($alias, '/') . '/';
        return isset($this->aliases[$alias]);
    }

    public function resolve(
        string $path
    ): string {
        if (isset($this->aliases[$path])) {
            return $this->aliases[$path];
        }

        if (
            !str_ends_with($path, '/') &&
            isset($this->aliases[$path . '/'])
        ) {
            return $this->aliases[$path . '/'];
        }

        foreach ($this->aliases as $alias => $target) {
            if (str_starts_with($path, $alias)) {
                return $target . substr($path, strlen($alias));
            }
        }

        return $path;
    }

    public function prettify(
        string $path
    ): string {
        foreach ($this->aliases as $alias => $target) {
            $alias = ltrim(trim($alias, '/'), '@');

            if (
                str_contains($alias, '/') ||
                !str_starts_with($path, $target)
            ) {
                continue;
            }

            return '@' . $alias . ':/' . substr($path, strlen($target));
        }

        return $path;
    }

    public function removeAlias(
        string $alias
    ): void {
        $alias = rtrim($alias, '/') . '/';
        unset($this->aliases[$alias]);
    }
}
