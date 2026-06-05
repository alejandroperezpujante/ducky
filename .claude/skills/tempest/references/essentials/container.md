# Container (Dependency Injection)

**Source:** `vendor/tempest/framework/docs/1-essentials/05-container.md`

## Autowiring

Constructor parameters are resolved automatically for controllers, console commands, event handlers, initializers, etc.

```php
final readonly class AircraftService
{
    public function __construct(
        private ExternalAircraftProvider $provider,
        private AircraftRepository $repository,
    ) {}
}
```

## Initializers

Control how a class/interface is constructed:

```php app/MarkdownInitializer.php
use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;

final readonly class MarkdownInitializer implements Initializer
{
    #[Singleton]  // optional — cache the result
    public function initialize(Container $container): MarkdownConverter
    {
        return new MarkdownConverter(/* ... */);
    }
}
```

**Key rule**: return type of `initialize()` determines what class/interface this handles. Can be union: `MarkdownConverter|Markdown`.

## Dynamic initializers

Match multiple classes at runtime:

```php app/RouteBindingInitializer.php
use Tempest\Container\DynamicInitializer;
use Tempest\Reflection\ClassReflector;

final class RouteBindingInitializer implements DynamicInitializer
{
    public function canInitialize(ClassReflector $class, null|string|UnitEnum $tag): bool
    {
        return $class->getType()->matches(Bindable::class);
    }

    public function initialize(ClassReflector $class, null|string|UnitEnum $tag, Container $container): object
    {
        // ...
    }
}
```

## Autowire attribute

For simple interface → implementation mapping:

```php app/AircraftService.php
use Tempest\Container\Autowire;

#[Autowire]
final readonly class AircraftService implements AircraftServiceInterface
{
    // Tempest links AircraftServiceInterface → AircraftService automatically
}
```

## Singletons

```php
use Tempest\Container\Singleton;

#[Singleton]
final readonly class Client { ... }
```

Tagged singletons:

```php
// Register
#[Singleton(tag: 'web')]
public function initialize(Container $container): Highlighter
{
    return new Highlighter(new CssTheme());
}

// Inject
public function __construct(
    #[Tag('web')] private Highlighter $highlighter
) {}

// Get directly
$container->get(Highlighter::class, tag: 'cli');
```

## `#[Inject]` on properties

```php
use Tempest\Container\Inject;

trait HasConsole
{
    #[Inject]
    private Console $console;
}
```

Useful in traits to avoid constructor conflicts.

## Decorators

```php
use Tempest\Container\Decorates;

#[Decorates(Repository::class)]
final readonly class CacheRepository implements Repository
{
    public function __construct(
        private Repository $repository,  // original injected here
        private Cache $cache,
    ) {}
}
```

## Proxy (lazy loading)

```php
use Tempest\Container\Proxy;

final readonly class BookController
{
    public function __construct(
        #[Proxy] private VerySlowClass $verySlowClass
    ) {}
}
```

## Container functions

```php
use function Tempest\Container\get;
use function Tempest\invoke;

$config = get(AppConfig::class);     // service locator (last resort)
$this->container->invoke(TrackAircraft::class, type: AircraftType::PC12);
```

## Gotchas

- `Initializer` auto-discovered; no registration needed.
- `#[Singleton]` on the class vs on the `initialize()` method — both work; method annotation is preferred for initializer classes.
- `#[Inject]` is a form of service location — prefer constructor injection when possible.
