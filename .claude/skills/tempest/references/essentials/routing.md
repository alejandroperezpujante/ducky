# Routing

**Source:** `vendor/tempest/framework/docs/1-essentials/01-routing.md`

## Basic route

```php app/HomeController.php
use Tempest\Router\Get;
use Tempest\View\View;
use function Tempest\View\view;

final readonly class HomeController
{
    #[Get(uri: '/home')]
    public function __invoke(): View
    {
        return view('./home.view.php');
    }
}
```

**All HTTP verb attributes**: `Get`, `Post`, `Delete`, `Put`, `Patch`, `Options`, `Connect`, `Trace`, `Head` — all in `Tempest\Router\`.

## Route parameters

```php
#[Get(uri: '/aircraft/{id}')]
public function show(int $id): View { ... }

// Optional: {?param}
#[Get(uri: '/aircraft/{?id}')]
public function index(?int $id): View { ... }

// Regex constraint
#[Get(uri: '/aircraft/{id:[0-9]+}')]
public function show(int $id): View { ... }
```

## Route binding

```php
use Tempest\Router\Bindable;

final class Aircraft implements Bindable
{
    public static function resolve(string $input): ?static
    {
        return query(self::class)->resolve($input);
    }
}

// Controller receives model, not scalar
#[Get('/aircraft/{aircraft}')]
public function show(Aircraft $aircraft): Response { ... }
```

## URI generation

```php
use function Tempest\Router\uri;

uri(HomeController::class);                          // /home
uri([AircraftController::class, 'show'], id: 1);    // /aircraft/1
```

Signed URIs:
```php
use function Tempest\Router\signed_uri;
use function Tempest\Router\temporary_signed_uri;

signed_uri([MailingListController::class, 'unsubscribe'], email: $email);
temporary_signed_uri(PasswordlessAuthController::class, duration: Duration::minutes(10), userId: $id);

// Validate: $this->uri->hasValidSignature($request)
```

## Request classes

```php app/RegisterAirportRequest.php
use Tempest\Http\Request;
use Tempest\Http\IsRequest;
use Tempest\Validation\Rules\HasLength;

final class RegisterAirportRequest implements Request
{
    use IsRequest;

    #[HasLength(min: 10, max: 120)]
    public string $name;
    public string $servedCity;
    public ?DateTime $registeredAt = null;
}
```

```php app/AirportController.php
#[Post(uri: '/airports/register')]
public function store(RegisterAirportRequest $request): Redirect
{
    $airport = map($request)->to(Airport::class)->save();
    return new Redirect(uri([self::class, 'show'], id: $airport->id));
}
```

Sensitive fields: `#[Tempest\Http\SensitiveField]` — stripped from session on validation error.

## Responses

```php
use Tempest\Http\Responses\Ok;
use Tempest\Http\Responses\Created;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Download;
use Tempest\Http\Responses\NotFound;
use Tempest\Http\GenericResponse;
use Tempest\Http\Status;

return new Redirect('/home');
return new Download($path);
return new GenericResponse(status: Status::CREATED, body: ['id' => $id]);
```

Custom response:
```php
use Tempest\Http\IsResponse;
use Tempest\Http\Response;

final class AircraftRegistered implements Response
{
    use IsResponse;
    public function __construct(Aircraft $a) {
        $this->status = Status::CREATED;
        $this->flash('success', "Registered {$a->icao_code}");
    }
}
```

## Middleware

```php app/ValidateWebhook.php
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;
use Tempest\Discovery\SkipDiscovery;

#[SkipDiscovery]  // per-route middleware: skip global discovery
final readonly class ValidateWebhook implements HttpMiddleware
{
    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        // ...
        return $next($request);
    }
}

// Apply to route:
#[Post('/slack/interaction', middleware: [ValidateWebhook::class])]

// Exclude global middleware:
#[Get('/health', without: [RateLimitMiddleware::class])]
```

Priority: `#[Priority(Priority::HIGH)]` from `Tempest\Support\Priority`.

## Route decorators

```php
use Tempest\Router\Prefix;
use Tempest\Router\WithMiddleware;
use Tempest\Router\WithoutMiddleware;
use Tempest\Router\Stateless;

#[Prefix('/api')]
#[WithMiddleware(AuthMiddleware::class)]
final readonly class ApiController { ... }

#[Stateless]  // no session/cookie overhead
#[Get('/rss')]
public function rss(): Response { ... }
```

## Session

```php
use Tempest\Http\Session\Session;

$this->session->get('key');
$this->session->set('key', $value);
$this->session->remove('key');
$this->session->flash('message', 'Saved!');
```

Session config: `session.config.php` returning `FileSessionConfig` / `DatabaseSessionConfig`.

## Defer

```php
use function Tempest\defer;

defer(fn () => event(new PageVisited($request->getUri())));
```

## Testing

```php
$this->http->get('/path')->assertOk()->assertSee('text');
$this->http->post('/path', ['field' => 'value'])->assertRedirect();
```
