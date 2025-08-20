<?php

/**
 * @package Monarch
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Kingdom\PureService;
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Monarch\Build;
use DecodeLabs\Monarch\Environment;
use DecodeLabs\Monarch\EnvironmentMode;
use DecodeLabs\Monarch\ExceptionLogger;
use DecodeLabs\Monarch\Paths;
use Throwable;

Monarch::setStartTime(microtime(true));

class Monarch
{
    protected static Paths $paths;
    protected static Kingdom $kingdom;
    protected static Environment $environment;
    protected static Build $build;
    protected static float $startTime;

    /**
     * @var array<string,ExceptionLogger>
     */
    protected static array $exceptionLoggers = [];

    /**
     * @var array<class-string<Service>,Service>
     */
    private static array $serviceCache = [];


    public static function getPaths(): Paths
    {
        return static::$paths ??= new Paths();
    }


    public static function setKingdom(
        Kingdom $kingdom
    ): void {
        static::$kingdom = $kingdom;

        if (isset(static::$paths)) {
            static::$kingdom->container->setFactory(
                Paths::class,
                fn () => static::$paths
            );
        }

        if (isset(static::$build)) {
            static::$kingdom->container->setFactory(
                Build::class,
                fn () => static::$build
            );
        }

        if (isset(static::$environment)) {
            static::$kingdom->container->setFactory(
                Environment::class,
                fn () => static::$environment
            );
        }
    }

    public static function getKingdom(): Kingdom
    {
        return static::$kingdom ?? throw Exceptional::Logic(
            'No kingdom registered'
        );
    }

    public static function hasKingdom(): bool
    {
        return isset(static::$kingdom);
    }

    public static function setBuild(
        Build $build
    ): void {
        static::$build = $build;

        if (isset(static::$kingdom)) {
            static::$kingdom->container->set(Build::class, $build);
        }
    }

    public static function getBuild(): Build
    {
        return static::$build ??= new class(static::$paths->root) implements Build {
            public protected(set) bool $compiled = false;
            public protected(set) ?int $time = null;
            public protected(set) int $cacheBuster = 0;

            public function __construct(
                public protected(set) string $path
            ) {
                $this->cacheBuster = $this->time ?? time();
            }
        };
    }

    public static function hasBuild(): bool
    {
        return isset(static::$build);
    }

    public static function setEnvironment(
        Environment $environment
    ): void {
        static::$environment = $environment;

        if (isset(static::$kingdom)) {
            static::$kingdom->container->set(Environment::class, $environment);
        }
    }

    public static function getEnvironment(): Environment
    {
        return static::$environment ??= new class(static::getBuild()->compiled) implements Environment {
            public protected(set) EnvironmentMode $mode = EnvironmentMode::Production;
            public protected(set) string $name = 'production';

            public function __construct(
                public protected(set) bool $compiled
            ) {
                $this->name = $this->compiled ? 'production' : 'development';
                $this->mode = $this->compiled ? EnvironmentMode::Production : EnvironmentMode::Development;
            }
        };
    }

    public static function hasEnvironment(): bool
    {
        return isset(static::$environment);
    }


    public static function isDevelopment(): bool
    {
        return static::$environment->mode === EnvironmentMode::Development;
    }

    public static function isTesting(): bool
    {
        return
            static::$environment->mode === EnvironmentMode::Testing ||
            static::$environment->mode === EnvironmentMode::Development;
    }

    public static function isProduction(): bool
    {
        return static::$environment->mode === EnvironmentMode::Production;
    }


    public static function setStartTime(
        float $time
    ): void {
        static::$startTime = $time;
    }

    public static function getStartTime(): float
    {
        return static::$startTime;
    }

    public static function getRunTime(): float
    {
        return microtime(true) - static::$startTime;
    }

    public static function getRunTimeFormatted(): string
    {
        return number_format(round(static::getRunTime() * 1000, 4), 2) . ' ms';
    }


    public static function registerExceptionLogger(
        ExceptionLogger $logger
    ): void {
        $key = get_class($logger);
        static::$exceptionLoggers[$key] = $logger;
    }

    public static function unregisterExceptionLogger(
        ExceptionLogger $logger
    ): void {
        $key = get_class($logger);
        unset(static::$exceptionLoggers[$key]);
    }

    public static function logException(
        Throwable $exception
    ): void {
        foreach (static::$exceptionLoggers as $logger) {
            $logger->logException($exception);
        }
    }





    /**
     * @template T of Service
     * @param class-string<T> $class
     * @return T
     */
    public static function getService(
        string $class
    ): Service {
        if (isset(self::$serviceCache[$class])) {
            /** @var T */
            return self::$serviceCache[$class];
        }

        try {
            return self::$serviceCache[$class] = static::$kingdom->getService($class);
        } catch (LogicException $e) {
            if (
                class_exists($class) &&
                is_a($class, PureService::class, true)
            ) {
                return self::$serviceCache[$class] = $class::providePureService();
            }

            throw $e;
        }
    }
}
