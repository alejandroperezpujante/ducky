# Package Development

**Source:** `vendor/tempest/framework/docs/5-extra-topics/01-package-development.md`

## Overview

A Tempest package = standard PHP Composer package that depends on a `tempest/*` package. Discovery happens automatically via Composer metadata.

No "service providers" — use discovery + initializers instead.

## Optional Tempest support

```json composer.json
{
    "extra": {
        "tempest": {
            "can-discover": true
        }
    }
}
```

## Prevent discovery of specific classes

```php
use Tempest\Discovery\SkipDiscovery;

#[SkipDiscovery]
final readonly class UserMigration implements MigratesUp { ... }
```

Exclude paths (for optional dependencies that may cause reflection errors):
```json composer.json
{
    "extra": {
        "tempest": {
            "ignore": ["src/OptionalDependency.php"]
        }
    }
}
```

## Installers (publish files to user project)

```php
use Tempest\Core\Installer;
use Tempest\Core\PublishesFiles;

final readonly class AuthInstaller implements Installer
{
    use PublishesFiles;

    private(set) string $name = 'auth';

    public function install(): void
    {
        $publishFiles = [
            __DIR__ . '/User.php' => src_path('User.php'),
            __DIR__ . '/UserMigration.php' => src_path('UserMigration.php'),
        ];

        foreach ($publishFiles as $source => $destination) {
            $this->publish(source: $source, destination: $destination);
        }

        $this->publishImports(); // adjust namespaces in published files
    }
}
```

User runs: `./tempest install <name>` → prompts destination, handles overwrite.

## Provider classes (hook into boot)

```php
use Tempest\Core\KernelEvent;
use Tempest\EventBus\EventHandler;

final readonly class MyPackageProvider
{
    public function __construct(private Container $container) {}

    #[EventHandler(KernelEvent::BOOTED)]
    public function initialize(): void
    {
        $this->container->config(/* ... */);
    }
}
```
