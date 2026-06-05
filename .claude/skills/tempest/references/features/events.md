# Event Bus

**Source:** `vendor/tempest/framework/docs/2-features/08-events.md`

## Define an event

```php app/AircraftRegistered.php
final readonly class AircraftRegistered
{
    public function __construct(public string $registration) {}
}

// Or an enum:
enum AircraftLifecycle { case REGISTERED; case RETIRED; }
```

## Dispatch

```php
use Tempest\EventBus\EventBus;
use function Tempest\event;

// Inject:
$this->eventBus->dispatch(new AircraftRegistered('LX-JFA'));

// Function (service locator):
event(new AircraftRegistered('LX-JFA'));
```

## Handle (global — auto-discovered)

```php app/AircraftObserver.php
use Tempest\EventBus\EventHandler;

final readonly class AircraftObserver
{
    #[EventHandler]
    public function onAircraftRegistered(AircraftRegistered $event): void
    {
        // ...
    }
}
```

## Handle (local — register in context)

```php
$this->eventBus->listen(function (UserSynced $event) {
    $this->console->keyValue($event->fullName, 'SYNCED');
});
```

## Middleware

```php app/EventLoggerMiddleware.php
use Tempest\EventBus\EventBusMiddleware;
use Tempest\EventBus\EventBusMiddlewareCallable;

final readonly class EventLoggerMiddleware implements EventBusMiddleware
{
    public function __invoke(string|object $event, EventBusMiddlewareCallable $next): void
    {
        $next($event);
        if ($event instanceof ShouldBeLogged) {
            $this->logger->info($event->getLogMessage());
        }
    }
}
```

Priority: `#[Priority(Priority::HIGH)]`. Non-global: `#[SkipDiscovery]`.

## Stop propagation

```php
use Tempest\EventBus\StopsPropagation;

#[StopsPropagation]
final class MyEvent {}

// Or on handler:
#[EventHandler]
#[StopsPropagation]
public function handle(OtherEvent $event): void { ... }
```

## Built-in events

- `KernelEvent::BOOTED` — framework finished bootstrapping
- `KernelEvent::SHUTDOWN` — before process exit
- Migration events: `MigrationMigrated`, `MigrationRolledBack`, `MigrationFailed`

## Testing

```php
$this->eventBus->recordEventDispatches();
$this->eventBus->preventEventHandling();

$this->eventBus->assertDispatched(AircraftRegistered::class);
$this->eventBus->assertDispatched(AircraftRegistered::class, count: 2);
$this->eventBus->assertDispatched(
    event: AircraftRegistered::class,
    callback: fn (AircraftRegistered $e) => $e->registration === 'LX-JFA'
);
$this->eventBus->assertNotDispatched(AircraftRegistered::class);
$this->eventBus->assertListeningTo(AircraftRegistered::class);
```
