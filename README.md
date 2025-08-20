# Monarch

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/monarch?style=flat)](https://packagist.org/packages/decodelabs/monarch)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/monarch.svg?style=flat)](https://packagist.org/packages/decodelabs/monarch)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/monarch.svg?style=flat)](https://packagist.org/packages/decodelabs/monarch)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/monarch/integrate.yml?branch=develop)](https://github.com/decodelabs/monarch/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/monarch?style=flat)](https://packagist.org/packages/decodelabs/monarch)

### A single shared source of truth for your PHP apps

Monarch provides a single shared source of truth for your PHP applications. It allow commonly referenced paths and items to be centralised into a predictable location, making it easier to manage accessing that data from code that needs to maintain minimal coupling to the rest of the application.

It acts as the top level oversight for your entire application space and acts as an orchestrator for your application's container and services within.

`Monarch` works in tandem with [Kingdom](https://github.com/decodelabs/kingdom) - it manages the _active_ `Kingdom` instance that in turn contains all of your application's services.

---

## Installation

Install via Composer:

```bash
composer require decodelabs/monarch
```

## Usage

Monarch should be populated in your bootstrap otherwise it will use reasonable defaults where possible. If you use `Genesis` to bootstrap your application, Monarch will be automatically populated for you.

For example:

```php
use DecodeLabs\Monarch;
$paths = Monarch::getPaths();

$paths->root = '/var/www/my-cool-app';
$paths->run = '/var/www/my-cool-app/dist';
$paths->localData = '/var/www/my-cool-app/data/local';
$paths->sharedData = '/var/www/my-cool-app/data/shared';

Monarch::setKingdom(new MyKingdom());
```

### Path aliasing

Monarch allows you to define aliases for commonly used paths. This is useful for avoiding hardcoded paths in your codebase. `@root` and `@run` are automatically defined for you, but you can define your own aliases as needed.

```php
$paths->alias('@components', '@root/components');
$paths->alias('@assets', '@root/assets');
```
You can then use these aliases in your code:

```php
$path = $paths->resolve('@components/MyComponent.php');
// /var/www/my-cool-app/components/MyComponent.php
```

### Services

Monarch provides a simple way to access `Kingdom`services from your application.
Services must implement the Kingdom `Service` interface and may provide the ability to self-instantiate.

```php
$service = Monarch::getService(MyService::class);
```

This method is a simple wrapper around the `getService()` method of the active `Kingdom` instance. It will only work if you supply an instance of the `Kingdom` interface in your bootstrap.

While this method is useful for quick access to services, it is not recommended for regular use as it is essentially an implementation of the Service Locator pattern. It _works_, but is inflexible and can lead to tight coupling between your code and the container.

Instead, the `Kingdom` instance provided to `Monarch` is also made available to [Slingshot](https://github.com/decodelabs/slingshot) which can then be used to automatically inject services into your code.

Many common DecodeLabs libraries use `Slingshot` in their architecture and allow arbitrary constructor parameters to be handled seamlessly.

## Licensing

Monarch is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
