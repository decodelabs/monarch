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

class Monarch implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Monarch';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;
    public static PathsPlugin $paths;
    /** @var ContainerPlugin|PluginWrapper<ContainerPlugin> $container */
    public static ContainerPlugin|PluginWrapper $container;

    public static function setApplicationName(?string $name): void {}
    public static function getApplicationName(): string {
        return static::$_veneerInstance->getApplicationName();
    }
    public static function replaceContainer(ContainerPlugin $container): void {}
};
