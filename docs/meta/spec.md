# Monarch — Package Specification

> **Cluster:** `runtime`
> **Language:** `php`
> **Milestone:** `m1`
> **Repo:** `https://github.com/decodelabs/monarch`
> **Role:** Single source of truth

## Overview

### Purpose

Monarch provides a single shared source of truth for PHP applications. It centralizes commonly referenced paths, configuration, and services into predictable locations, making it easier to manage application state with minimal coupling. Monarch enables:

- Active Kingdom instance management
- Path management with aliasing
- Environment mode detection and management
- Build information tracking
- Service access via service locator pattern
- Exception logging registration
- Application runtime tracking

Monarch acts as the top-level orchestrator for the entire application space, managing the active Kingdom instance that contains all application services.

### Non-Goals

- Monarch does not provide dependency injection (delegates to Kingdom and Slingshot)
- It does not provide configuration management (delegates to Dovetail or other config packages)
- It does not provide service container implementation (delegates to Kingdom and Pandora)
- It does not handle application routing or dispatching
- It does not provide caching or storage
- It does not provide template rendering or view management

## Role in the Ecosystem

### Cluster & Positioning

Monarch belongs to the **runtime** cluster, providing core application state management. It sits at the foundation of the runtime stack, orchestrating Kingdom instances and providing global access to application paths, environment, and services.

### Usage Contexts

Monarch is used for:

- Application bootstrapping and initialization
- Path resolution and aliasing
- Environment detection and configuration
- Service access when dependency injection is not available
- Build information tracking
- Exception logging coordination
- Runtime performance tracking

## Public Surface

### Key Types

- **`Monarch`** — Main static class providing global access to paths, kingdom, environment, build, and services.

- **`Monarch\Paths`** — Class for managing application paths with aliasing support. Provides properties for root, run, working, localData, sharedData, and subjectRoot.

- **`Monarch\Environment`** — Interface for environment information. Provides name and mode properties.

- **`Monarch\EnvironmentMode`** — Enum defining environment modes: `Development`, `Testing`, `Production`.

- **`Monarch\Build`** — Interface for build information. Provides compiled status, path, time, and cacheBuster properties.

- **`Monarch\ExceptionLogger`** — Interface for exception logging. Defines `logException()` method.

### Main Entry Points

- **`Monarch::getPaths(): Paths`** — Gets the global paths instance. Creates default instance if not set.

- **`Monarch::setKingdom(Kingdom $kingdom): void`** — Sets the active Kingdom instance. Registers paths, build, and environment in the kingdom's container.

- **`Monarch::getKingdom(): Kingdom`** — Gets the active Kingdom instance. Throws exception if not set.

- **`Monarch::hasKingdom(): bool`** — Checks if a Kingdom instance is registered.

- **`Monarch::setBuild(Build $build): void`** — Sets the build information instance. Registers it in the kingdom's container if kingdom is set.

- **`Monarch::getBuild(): Build`** — Gets the build information instance. Creates default instance if not set.

- **`Monarch::hasBuild(): bool`** — Checks if build information is set.

- **`Monarch::setEnvironment(Environment $environment): void`** — Sets the environment instance. Registers it in the kingdom's container if kingdom is set.

- **`Monarch::getEnvironment(): Environment`** — Gets the environment instance. Creates default instance if not set.

- **`Monarch::hasEnvironment(): bool`** — Checks if environment is set.

- **`Monarch::isDevelopment(): bool`** — Checks if environment mode is Development.

- **`Monarch::isTesting(): bool`** — Checks if environment mode is Testing or Development.

- **`Monarch::isProduction(): bool`** — Checks if environment mode is Production.

- **`Monarch::setStartTime(float $time): void`** — Sets the application start time.

- **`Monarch::getStartTime(): float`** — Gets the application start time.

- **`Monarch::getRunTime(): float`** — Gets the elapsed runtime in seconds.

- **`Monarch::getRunTimeFormatted(): string`** — Gets the elapsed runtime formatted as milliseconds.

- **`Monarch::registerExceptionLogger(ExceptionLogger $logger): void`** — Registers an exception logger.

- **`Monarch::unregisterExceptionLogger(ExceptionLogger $logger): void`** — Unregisters an exception logger.

- **`Monarch::logException(Throwable $exception): void`** — Logs an exception to all registered loggers.

- **`Monarch::getService(string $class): Service`** — Gets a service from the active Kingdom or creates a PureService if no kingdom is set. Caches service instances.

- **`Paths::root: string`** — Property for application root path. Auto-creates alias `@root`.

- **`Paths::run: string`** — Property for runtime/build path. Auto-creates alias `@run`.

- **`Paths::working: string`** — Property for current working directory. Changes PHP's working directory when set.

- **`Paths::localData: string`** — Property for local data directory. Defaults to `root/data/local`.

- **`Paths::sharedData: string`** — Property for shared data directory. Defaults to `root/data/shared`.

- **`Paths::subjectRoot: ?string`** — Property for subject root path. Auto-creates alias `@subject-root`.

- **`Paths::alias(string $alias, string $path): void`** — Creates a path alias.

- **`Paths::hasAlias(string $alias): bool`** — Checks if an alias exists.

- **`Paths::resolve(string $path): string`** — Resolves a path, replacing aliases with actual paths.

- **`Paths::prettify(string $path): string`** — Converts an absolute path to its alias representation if possible.

- **`Paths::removeAlias(string $alias): void`** — Removes a path alias.

## Dependencies

### Decode Labs

- **`enumerable`** — Used for `EnvironmentMode` enum implementation via `ValueString` interface.

- **`exceptional`** — Used for exception handling throughout the package.

- **`kingdom`** — Used for Kingdom interface and service management.

### External

- **`psr/container`** — PSR-11 container interface (indirect dependency via Kingdom).

## Behaviour & Contracts

### Invariants

- Only one Kingdom instance can be active at a time
- Path aliases are stored with trailing slashes for matching
- Paths are normalized (trailing slashes removed) when set
- Environment mode determines compiled status (production = compiled)
- Service instances are cached after first access
- Start time is set automatically when Monarch class is loaded
- Exception loggers are keyed by class name
- Default paths are created lazily on first access
- Default build and environment are created lazily if not set

### Input & Output Contracts

- **`Monarch::getPaths(): Paths`** — Returns paths instance. Creates default instance with working directory detection if not set.

- **`Monarch::setKingdom(Kingdom $kingdom): void`** — Sets active kingdom. Registers paths, build, and environment in kingdom's container if they exist.

- **`Monarch::getKingdom(): Kingdom`** — Returns active kingdom. Throws `Logic` exception if not set.

- **`Monarch::getService(string $class): Service`** — Returns service instance. Uses kingdom if available, otherwise creates PureService. Caches instances. Throws `Logic` if no kingdom and class is not PureService.

- **`Paths::root: string`** — Returns root path. Defaults to working directory. Auto-creates `@root` alias.

- **`Paths::run: string`** — Returns run path. Defaults to root path. Auto-creates `@run` alias.

- **`Paths::working: string`** — Returns working directory. Detects from `DOCUMENT_ROOT` or `getcwd()`. Changes PHP's working directory when set.

- **`Paths::alias(string $alias, string $path): void`** — Creates alias. Normalizes alias and path (adds trailing slashes). Resolves path before storing.

- **`Paths::resolve(string $path): string`** — Resolves path by replacing aliases. Returns original path if no alias matches.

- **`Paths::prettify(string $path): string`** — Converts absolute path to alias representation. Returns original path if no matching alias found.

- **`Monarch::getEnvironment(): Environment`** — Returns environment instance. Creates default instance based on build compiled status if not set.

- **`Monarch::getBuild(): Build`** — Returns build instance. Creates default instance with root path if not set.

- **`Monarch::isDevelopment(): bool`** — Returns true if environment mode is Development.

- **`Monarch::isTesting(): bool`** — Returns true if environment mode is Testing or Development.

- **`Monarch::isProduction(): bool`** — Returns true if environment mode is Production.

- **`Monarch::getRunTime(): float`** — Returns elapsed runtime in seconds since start time.

- **`Monarch::logException(Throwable $exception): void`** — Logs exception to all registered loggers. Never throws exceptions.

## Error Handling

Monarch uses the Exceptional pattern for error handling. Key exception types:

- **`Logic`** — Thrown when `getKingdom()` or `getService()` is called without a registered kingdom (and service is not PureService).

- **`Runtime`** — Thrown when working directory cannot be determined.

Exceptions preserve the original service context and include detailed error messages. Exception logging never throws exceptions.

## Configuration & Extensibility

### Extension Points

- **Custom Environment Implementations** — Implement `Environment` interface to provide custom environment detection and configuration.

- **Custom Build Implementations** — Implement `Build` interface to provide custom build information.

- **Exception Loggers** — Implement `ExceptionLogger` interface to provide custom exception logging.

- **Path Aliases** — Define custom path aliases via `Paths::alias()` for application-specific paths.

### Configuration

- **Path Setup** — Paths are configured during bootstrap. Root and run paths should be set explicitly.

- **Kingdom Registration** — Kingdom instance should be registered during bootstrap via `setKingdom()`.

- **Environment Detection** — Environment is auto-detected from build compiled status, but can be set explicitly.

- **Build Information** — Build information can be set explicitly or defaults to root path with current time.

- **Start Time** — Start time is automatically set when Monarch class is loaded, but can be overridden.

- **Exception Loggers** — Exception loggers are registered via `registerExceptionLogger()` during bootstrap.

## Interactions with Other Packages

- **Kingdom** — Monarch manages the active Kingdom instance and provides service access via `getService()`.

- **Slingshot** — Monarch's Kingdom instance is made available to Slingshot for automatic dependency injection.

- **Genesis** — Genesis automatically populates Monarch during application bootstrap.

- **Dovetail** — Uses Monarch paths for configuration file resolution.

- **Archetype** — Uses Monarch paths for class resolution and library loading.

- **Systemic** — Uses Monarch paths for file system operations.

- **Stash** — Uses Monarch paths for cache directory resolution.

- **Iota** — Uses Monarch paths for code repository directories.

## Usage Examples

### Basic Setup

```php
use DecodeLabs\Monarch;

// Set paths
$paths = Monarch::getPaths();
$paths->root = '/var/www/my-app';
$paths->run = '/var/www/my-app/dist';
$paths->localData = '/var/www/my-app/data/local';
$paths->sharedData = '/var/www/my-app/data/shared';

// Register kingdom
Monarch::setKingdom(new MyKingdom());
```

### Path Aliasing

```php
use DecodeLabs\Monarch;

$paths = Monarch::getPaths();

// Define aliases
$paths->alias('@components', '@root/components');
$paths->alias('@assets', '@root/assets');
$paths->alias('@public', '@run/public');

// Resolve paths
$componentPath = $paths->resolve('@components/MyComponent.php');
// /var/www/my-app/components/MyComponent.php

// Prettify paths
$pretty = $paths->prettify('/var/www/my-app/components/MyComponent.php');
// @components:/MyComponent.php
```

### Service Access

```php
use DecodeLabs\Monarch;

// Get service from active kingdom
$service = Monarch::getService(MyService::class);

// Service is cached after first access
$service2 = Monarch::getService(MyService::class); // Returns cached instance
```

### Environment Detection

```php
use DecodeLabs\Monarch;

// Check environment mode
if (Monarch::isDevelopment()) {
    // Development-specific code
}

if (Monarch::isTesting()) {
    // Testing or development code
}

if (Monarch::isProduction()) {
    // Production-specific code
}

// Get environment
$env = Monarch::getEnvironment();
echo $env->name; // 'development' or 'production'
echo $env->mode; // EnvironmentMode enum
```

### Build Information

```php
use DecodeLabs\Monarch;

// Get build information
$build = Monarch::getBuild();
echo $build->path; // Build directory path
echo $build->compiled; // true or false
echo $build->time; // Build timestamp or null
echo $build->cacheBuster; // Cache buster value
```

### Runtime Tracking

```php
use DecodeLabs\Monarch;

// Get runtime
$runtime = Monarch::getRunTime(); // float in seconds
$formatted = Monarch::getRunTimeFormatted(); // "123.45 ms"
```

### Exception Logging

```php
use DecodeLabs\Monarch;
use DecodeLabs\Monarch\ExceptionLogger;

class MyLogger implements ExceptionLogger
{
    public function logException(Throwable $exception): void
    {
        // Log exception
    }
}

// Register logger
Monarch::registerExceptionLogger(new MyLogger());

// Log exceptions
try {
    // Code that may throw
} catch (Throwable $e) {
    Monarch::logException($e);
}
```

### Path Properties

```php
use DecodeLabs\Monarch;

$paths = Monarch::getPaths();

// Set paths
$paths->root = '/var/www/app';
$paths->run = '/var/www/app/dist';
$paths->working = '/var/www/app';
$paths->localData = '/var/www/app/data/local';
$paths->sharedData = '/var/www/app/data/shared';
$paths->subjectRoot = '/var/www/subject';

// Access paths
echo $paths->root; // /var/www/app
echo $paths->run; // /var/www/app/dist
echo $paths->localData; // /var/www/app/data/local
```

## Implementation Notes (for Contributors)

### Architecture

- **Static State Management** — Monarch uses static properties to maintain global state. This provides convenient access but requires careful initialization.

- **Lazy Initialization** — Paths, build, and environment instances are created lazily on first access, providing sensible defaults.

- **Kingdom Integration** — When a Kingdom is set, Monarch automatically registers paths, build, and environment in the kingdom's container for dependency injection.

- **Service Caching** — Service instances are cached after first access to avoid repeated resolution.

- **Path Aliasing** — Aliases are stored with trailing slashes for efficient prefix matching during resolution.

- **Working Directory Management** — Setting the working path changes PHP's current working directory, affecting relative path operations.

- **Environment Detection** — Environment mode is auto-detected from build compiled status (compiled = production, not compiled = development).

- **Start Time Tracking** — Start time is automatically set when the Monarch class is loaded, providing accurate runtime tracking.

### Performance Considerations

- Service instances are cached to avoid repeated resolution
- Path resolution uses prefix matching for efficient alias lookup
- Lazy initialization defers work until needed
- Static properties provide fast access without object instantiation

### Design Decisions

- **Static Facade** — Using static methods provides convenient global access while maintaining a single source of truth.

- **Lazy Initialization** — Creating default instances lazily allows Monarch to work without explicit setup while still allowing customization.

- **Kingdom Integration** — Automatically registering paths, build, and environment in the kingdom's container enables dependency injection while maintaining global access.

- **Service Caching** — Caching service instances improves performance and ensures singleton behavior.

- **Path Aliasing** — Using aliases provides flexibility and avoids hardcoded paths throughout the codebase.

- **Environment Detection** — Auto-detecting environment from build status provides sensible defaults while allowing explicit configuration.

- **Exception Logging** — Providing a centralized exception logging mechanism allows consistent error handling across the application.

## Testing & Quality

**Code Quality:** 4.5/5 — Excellent, mature codebase with comprehensive functionality and solid architecture.

**README Quality:** 3/5 — Good documentation with clear usage examples covering main use cases.

**Documentation:** 0/5 — No formal documentation beyond README.

**Tests:** 0/5 — No test suite currently.

See `composer.json` for supported PHP versions.

## Roadmap & Future Ideas

- Enhanced documentation and API reference
- Test suite implementation
- Additional path management features
- Environment variable integration
- Configuration management integration
- Performance optimizations
- Additional runtime metrics
- Path validation and normalization improvements

## References

- [Decode Labs Chorus](https://github.com/decodelabs/chorus)
- [Monarch Repository](https://github.com/decodelabs/monarch)
- [Kingdom Repository](https://github.com/decodelabs/kingdom)

