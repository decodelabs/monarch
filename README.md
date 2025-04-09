# Monarch

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/monarch?style=flat)](https://packagist.org/packages/decodelabs/monarch)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/monarch.svg?style=flat)](https://packagist.org/packages/decodelabs/monarch)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/monarch.svg?style=flat)](https://packagist.org/packages/decodelabs/monarch)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/monarch/integrate.yml?branch=develop)](https://github.com/decodelabs/monarch/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/monarch?style=flat)](https://packagist.org/packages/decodelabs/monarch)

### A single shared source of truth for your PHP apps

Monarch provides a single shared source of truth for your PHP applications. It allow commonly referenced paths and items to be centralised into a predictable location, making it easier to manage accessing that data from code that needs to maintain minimal coupling to the rest of the application.

_Get news and updates on the [DecodeLabs blog](https://blog.decodelabs.com)._

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

Monarch::setApplicationName('My Cool App');
Monarch::$paths->root = '/var/www/my-cool-app';
Monarch::$paths->run = '/var/www/my-cool-app/dist';
Monarch::$paths->localData = '/var/www/my-cool-app/data/local';
Monarch::$paths->sharedData = '/var/www/my-cool-app/data/shared';
```

### Path aliasing

Monarch allows you to define aliases for commonly used paths. This is useful for avoiding hardcoded paths in your codebase. `@root` and `@run` are automatically defined for you, but you can define your own aliases as needed.

```php
use DecodeLabs\Monarch;

Monarch::$paths->alias('@components', '@root/components');
Monarch::$paths->alias('@assets', '@root/assets');
```
You can then use these aliases in your code:

```php
use DecodeLabs\Monarch;
$path = Monarch::$paths->resolve('@components/MyComponent.php');
// /var/www/my-cool-app/components/MyComponent.php
```

### Container

Monarch attempts to centralise a PSR container for easy access to all components of your app.
Due to the PSR spec not providing an interface for _setting_ items, care must be taken when storing objects as the container instance could be of any type; the only guarantee is that it implements `Psr\Container\ContainerInterface`.

If you use `Genesis` to bootstrap your application, Monarch will contain a `Pandora` container, otherwise Monarch will use a simple stand-in container that implements the `Psr\Container\ContainerInterface` interface.

```php
use DecodeLabs\Monarch;
use DecodeLabs\Pandora\Container;

if(Monarch::$container instanceof Container) {
    Monarch::$container->bind('myService', new MyService());
}
```

If you are working in a `Fabric` app, it is better to reference `Fabric::$container` as that is guaranteed to be a `Pandora` container. However libraries should not be coupled to `Fabric` and should use the read-oriented methods of the Monarch interface instead.

```php
use DecodeLabs\Monarch;

$service = Monarch::$container->get('myService');
```

## Licensing

Monarch is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
