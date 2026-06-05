# Discovery

**Source:** `vendor/tempest/framework/docs/1-essentials/05-discovery.md`

## What is it

Tempest scans all PSR-4 namespaces and vendor packages on boot. No manual registration. Works via Composer metadata.

## Production

Cache discovery before deploy:

```sh
php tempest discovery:generate --no-interaction
php tempest cache:clear --all
```

Or add to `composer.json`:
```json
{
    "scripts": {
        "post-package-update": ["@php tempest discovery:generate"]
    }
}
```

## Troubleshoot

```sh
php tempest discovery:clear   # clears cache (rebuilt on next boot)
php tempest discovery:generate  # force regenerate
# Nuclear option: rm -rf .tempest/cache/discovery
```

Disable cache (useful when working on vendor packages):
```env .env
DISCOVERY_CACHE=false
```

## Skip discovery

```php
use Tempest\Discovery\SkipDiscovery;

#[SkipDiscovery]
final readonly class CautionMiddleware implements ConsoleMiddleware { ... }

// Except for specific discovery classes:
#[SkipDiscovery(except: [MigrationDiscovery::class])]
final class HiddenMigration implements MigratesUp { ... }
```

Config-based skip:
```php src/discovery.config.php
use Tempest\Core\DiscoveryConfig;

return new DiscoveryConfig()
    ->skipClasses(SomeClass::class)
    ->skipPaths(__DIR__ . '/Fixtures/HiddenThing.php');
```

## Custom discovery

```php EventBusDiscovery.php
use Tempest\Discovery\Discovery;
use Tempest\Discovery\IsDiscovery;

final class EventBusDiscovery implements Discovery
{
    use IsDiscovery;

    public function __construct(private EventBusConfig $eventBusConfig) {}

    public function discover(DiscoveryLocation $location, ClassReflector $class): void
    {
        foreach ($class->getPublicMethods() as $method) {
            $handler = $method->getAttribute(EventHandler::class);
            if ($handler) {
                $this->discoveryItems->add($location, [$method, $handler]);
            }
        }
    }

    public function apply(): void
    {
        foreach ($this->discoveryItems as [$method, $handler]) {
            $this->eventBusConfig->addHandler($method, $handler);
        }
    }
}
```

## File discovery

```php ViteDiscovery.php
use Tempest\Discovery\DiscoversPath;

final class ViteDiscovery implements Discovery, DiscoversPath
{
    use IsDiscovery;

    public function discover(DiscoveryLocation $l, ClassReflector $c): void { return; }

    public function discoverPath(DiscoveryLocation $location, string $path): void
    {
        if (! str($path)->beforeLast('.')->endsWith('.entrypoint')) return;
        $this->discoveryItems->add($location, [$path]);
    }

    public function apply(): void
    {
        foreach ($this->discoveryItems as [$path]) {
            $this->viteConfig->addEntrypoint($path);
        }
    }
}
```

## Built-in discovery classes

| Class | Discovers |
|-------|-----------|
| `RouteDiscovery` | `#[Get]`, `#[Post]` etc. → routes |
| `ConsoleCommandDiscovery` | `#[ConsoleCommand]` → CLI commands |
| `ScheduleDiscovery` | `#[Schedule]` → scheduled tasks |
| `EventBusDiscovery` | `#[EventHandler]` → event handlers |
| `CommandBusDiscovery` | `#[CommandHandler]` → command handlers |
| `MigrationDiscovery` | `MigratesUp`/`MigratesDown` → migrations |
| `InitializerDiscovery` | `Initializer`/`DynamicInitializer` → DI |
| `ViewComponentDiscovery` | `x-*.view.php` → view components |
| `ViteDiscovery` | `*.entrypoint.{ts,js,css}` → Vite entrypoints |
| `MapperDiscovery` | `Mapper` impl → mappers |
| `PolicyDiscovery` | `#[Policy]` → access control policies |
