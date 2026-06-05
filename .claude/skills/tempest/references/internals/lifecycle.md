# Framework Lifecycle

**Source:** `vendor/tempest/framework/docs/4-internals/01-lifecycle.md`

## Boot sequence

Entry points: `public/index.php` (web, `HttpApplication`) or `./tempest` (console, `ConsoleApplication`).

Both create `FrameworkKernel`:
1. Load environment, exception handler, configure container
2. Start discovery: `LoadDiscoveryLocations` + `LoadDiscoveryClasses`
3. Register config files: `LoadConfig`
4. Fire `KernelEvent::BOOTED`

## Shutdown

`kernel->shutdown()` called at end of HTTP/console lifecycle and in exception handlers:
1. Run deferred tasks (registered with `defer()`)
2. Dispatch `KernelEvent::SHUTDOWN`
3. Cleanup
4. Terminate process

## Hook into boot

```php
use Tempest\Core\KernelEvent;
use Tempest\EventBus\EventHandler;

final readonly class MyPackageProvider
{
    public function __construct(private Container $container) {}

    #[EventHandler(KernelEvent::BOOTED)]
    public function initialize(): void
    {
        // Register things, wire up dependencies
        $this->container->config(/* ... */);
    }
}
```
