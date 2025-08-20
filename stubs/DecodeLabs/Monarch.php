<?php

/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */

namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Monarch\Context as Inst;
use DecodeLabs\Monarch\Paths as PathsPlugin;
use Psr\Container\ContainerInterface as ContainerPlugin;
use DecodeLabs\Veneer\Plugin\Wrapper as PluginWrapper;
use DecodeLabs\Monarch\EnvironmentMode as Ref0;
use DecodeLabs\Monarch\ExceptionLogger as Ref1;
use Throwable as Ref2;

class Monarch implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Monarch';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;
    public static PathsPlugin $paths;
    /** @var ContainerPlugin|PluginWrapper<ContainerPlugin> $container */
    public static ContainerPlugin|PluginWrapper $container;

    public static function isDevelopment(): bool
    {
        return static::$_veneerInstance->isDevelopment();
    }
    public static function isTesting(): bool
    {
        return static::$_veneerInstance->isTesting();
    }
    public static function isProduction(): bool
    {
        return static::$_veneerInstance->isProduction();
    }
    public static function registerExceptionLogger(Ref1 $logger): void
    {
    }
    public static function unregisterExceptionLogger(Ref1 $logger): void
    {
    }
    public static function logException(Ref2 $exception): void
    {
    }
};
