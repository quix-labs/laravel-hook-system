# Laravel Hook System
[![Latest Version on Packagist](https://img.shields.io/packagist/v/quix-labs/laravel-hook-system.svg?style=flat-square)](https://packagist.org/packages/quix-labs/laravel-hook-system)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/quix-labs/laravel-hook-system/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/quix-labs/laravel-hook-system/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/quix-labs/laravel-hook-system/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/quix-labs/laravel-hook-system/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/quix-labs/laravel-hook-system.svg?style=flat-square)](https://packagist.org/packages/quix-labs/laravel-hook-system)
___
The `quix-labs/laravel-hook-system` package provides a hook system for Laravel.

This system allows intercepting and manipulating specific actions in your application.
___

## Requirements
* PHP >= 8.1
* Laravel 10.x|11.x

## Installation

You can install this package via Composer:

```bash
composer require quix-labs/laravel-hook-system
```

## Hook usage

### Creating a Hook

A hook is a class that extends `QuixLabs\LaravelHookSystem\Hook`:

```php
class GetString extends \QuixLabs\LaravelHookSystem\Hook
{
    public function __construct(public string &$string)
    {
    }
}
```

### Registering Hooks

In the `register` method of your ServiceProvider:

```php
use QuixLabs\LaravelHookSystem\Facades\HookManager;
use Workbench\App\Hooks\GetString;

class YourProvider{
    public function register()
    {
        HookManager::registerHook(GetString::class);
    }
}

```

### Executing a Hook

To execute a hook, `QuixLabs\LaravelHookSystem\Hook` implements the static `send` method:

```php
class YourController
{
    public function index()
    {
        $string = "";
         \Workbench\App\Hooks\GetString::send($string);
        return $string;
    }
}
```

## Interceptor usage

### Creating an Interceptor

An interceptor is a class with a static method intercepted via an `#[Intercept]` attribute:

```php
use Illuminate\Support\Str;
use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;

class AppendRandomString
{
    #[Intercept(\Workbench\App\Hooks\GetString::class)]
    public static function appendRandomString(GetString $hook): void
    {
        $hook->string .= Str::random(16);
    }
    
    # You can specify action when hook not found (THROW_ERROR, SKIP or REGISTER_HOOK)
    #[Intercept(\Workbench\App\Hooks\GetString::class, ActionWhenMissing::THROW_ERROR)]
    public static function appendStringRequired(GetString $hook): void
    {
        $hook->string .= Str::random(16);
    }
    
    # You can also specify execution priority using third argument
    #[Intercept(\Workbench\App\Hooks\GetString::class, ActionWhenMissing::SKIP, 100)]
    public static function appendRandomStringAtTheEnd(GetString $hook): void
    {
        $hook->string .= Str::random(16);
    }
}
```

### Registering Interceptors

In the `boot` method of your ServiceProvider:

```php
use QuixLabs\LaravelHookSystem\Facades\HookManager;

class YourProvider{
    public function boot()
    {
        HookManager::registerInterceptor(\App\Interceptors\AppendRandomString::class);
    }
}
```

## Artisan Commands

The package adds three Artisan commands to manage the hooks:

* `hooks:status` :  Displays the status of hooks and interceptors.
* `hooks:cache` : Caches the hooks and interceptors.
* `hooks:clear` : Clears the hooks and interceptors cache.


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [COLANT Alan](https://github.com/alancolant)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
