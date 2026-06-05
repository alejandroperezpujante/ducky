# Standalone Components

**Source:** `vendor/tempest/framework/docs/5-extra-topics/02-standalone-components.md`

Individual Tempest packages can be used in isolation.

## tempest/console

```sh
composer require tempest/console
```

See `references/packages/console.md`.

## tempest/http

```sh
composer require tempest/http
```

```php public/index.php
use Tempest\Router\HttpApplication;
require_once __DIR__ . '/../vendor/autoload.php';
HttpApplication::boot(root: __DIR__ . '/../')->run();
```

## tempest/container

```sh
composer require tempest/container
```

```php
$container = new Tempest\Container\GenericContainer();
$container->addInitializer(FooInitializer::class);
$foo = $container->get(Foo::class);
```

No discovery — register initializers manually.

## tempest/debug

```sh
composer require tempest/debug
```

Provides `ld()`, `lw()`, `ll()`. In full Tempest: also writes to log files.

## tempest/view

See views reference for `TempestViewRenderer::make()` standalone setup.

## tempest/event-bus

```sh
composer require tempest/event-bus
```

```php
$container = Tempest::boot();
$eventBus = $container->get(\Tempest\EventBus\EventBus::class);
$eventBus->dispatch(new MyEvent());
// Or: \Tempest\event(new MyEvent());
```

## tempest/command-bus

```sh
composer require tempest/command-bus
```

```php
$container = Tempest::boot();
$commandBus = $container->get(\Tempest\CommandBus\CommandBus::class);
$commandBus->dispatch(new MyCommand());
// Or: \Tempest\command(new MyCommand());
```

## tempest/mapper

```sh
composer require tempest/mapper
```

```php
Tempest::boot();
$foo = map(['name' => 'Hi'])->to(Foo::class);
```
