<?php

/**
 * @package Monarch
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Monarch;

use DecodeLabs\Monarch;
use DecodeLabs\Veneer;
use DecodeLabs\Veneer\Plugin;
use Psr\Container\ContainerInterface;

class Context
{
    #[Plugin]
    public Paths $paths;

    #[Plugin]
    public ContainerInterface $container;

    protected ?string $applicationName = null;
    protected EnvironmentMode $environmentMode = EnvironmentMode::Production;

    public function __construct()
    {
        $this->paths = new Paths();
        $this->replaceContainer(new Container());
    }



    public function setApplicationName(
        ?string $name
    ): void {
        $this->applicationName = $name;
    }

    public function getApplicationName(): string
    {
        return $this->applicationName ?? 'My App';
    }


    public function setEnvironmentMode(
        EnvironmentMode $mode
    ): void {
        $this->environmentMode = $mode;
    }

    public function getEnvironmentMode(): EnvironmentMode
    {
        return $this->environmentMode;
    }

    /**
     * Is running in development mode
     */
    public function isDevelopment(): bool
    {
        return $this->environmentMode === EnvironmentMode::Development;
    }

    /**
     * Is running in testing mode (or development)
     */
    public function isTesting(): bool
    {
        return
            $this->environmentMode === EnvironmentMode::Testing ||
            $this->environmentMode === EnvironmentMode::Development;
    }

    /**
     * Is running in production mode
     */
    public function isProduction(): bool
    {
        return $this->environmentMode === EnvironmentMode::Production;
    }


    public function replaceContainer(
        ContainerInterface $container
    ): void {
        $this->container = $container;
        Monarch::$container = $container;
        Veneer::setContainer($this->container);
    }
}

// Register the Veneer facade
Veneer\Manager::getGlobalManager()->register(
    Context::class,
    Monarch::class
);
