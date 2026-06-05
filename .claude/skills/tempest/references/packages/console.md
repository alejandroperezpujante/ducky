# Console (standalone: tempest/console)

**Source:** `vendor/tempest/framework/docs/3-packages/02-console.md`

Use Tempest's console as a standalone package outside full Tempest framework.

```sh
composer require tempest/console
```

## Boot

```php ./my-cli
#!/usr/bin/env php
<?php

use Tempest\Console\ConsoleApplication;

require_once __DIR__ . '/vendor/autoload.php';

ConsoleApplication::boot()->run();

// With extra discovery:
ConsoleApplication::boot(
    discoveryLocations: [
        new DiscoveryLocation(namespace: 'MyApp\\', path: __DIR__ . '/src'),
    ],
)->run();
```

## Commands

Same as in full Tempest — `#[ConsoleCommand]` on any method, auto-discovered from `composer.json` PSR-4 autoload paths.

## Discovery sources

1. Core Tempest packages
2. Vendor packages with `require tempest/*` or `extra.tempest.can-discover = true`
3. PSR-4 autoload paths in `composer.json`

For full command docs, see `references/essentials/console-commands.md`.
