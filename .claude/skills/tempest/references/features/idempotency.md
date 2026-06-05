# Idempotency

**Source:** `vendor/tempest/framework/docs/2-features/19-idempotency.md`

Prevents duplicate side effects for POST/PATCH routes and command bus commands.

## Idempotent routes

```php app/OrderController.php
use Tempest\Router\Post;
use Tempest\Idempotency\Attributes\Idempotent;
use Tempest\Idempotency\Attributes\IdempotentRoute;

final readonly class OrderController
{
    #[Post('/orders')]
    #[Idempotent]                          // basic
    // #[Idempotent(ttlInSeconds: 172_800)] // override TTL
    // #[IdempotentRoute(requireKey: true)] // require header
    public function create(CreateOrderRequest $request): Response { ... }
}

// Class-level (all POST/PATCH routes):
#[Idempotent]
final readonly class ApiOrderController { ... }
```

Client sends: `Idempotency-Key: <uuid>` header.

**Only POST and PATCH supported.** GET/PUT/DELETE throw `IdempotencyMethodWasNotSupported`.

## Scope resolver (REQUIRED)

```php app/UserIdempotencyScopeResolver.php
use Tempest\Idempotency\IdempotencyScopeResolver;

final readonly class UserIdempotencyScopeResolver implements IdempotencyScopeResolver
{
    public function resolve(Request $request): string
    {
        return (string) $this->auth->currentUser()->id;
    }
}
```

Without a scope resolver → middleware fails at construction.

## Response behavior

| Scenario | Status |
|----------|--------|
| No existing record | Execute normally, cache response |
| Same key + same payload | Replay cached response + `idempotency-replayed: true` header |
| Same key + different payload | 422 |
| Pending (in progress) | 409 + `retry-after: 1` header |
| Missing key (when required) | 400 |

## Idempotent commands

```php
use Tempest\Idempotency\Attributes\Idempotent;
use Tempest\Idempotency\HasIdempotencyKey;

#[Idempotent]
final readonly class ImportInvoicesCommand
{
    public function __construct(public string $vendorId, public string $month) {}
}

// Explicit key:
#[Idempotent]
final readonly class ProcessPaymentCommand implements HasIdempotencyKey
{
    public function getIdempotencyKey(): string { return $this->paymentId; }
}
```

## Config

```php app/idempotency.config.php
use Tempest\Idempotency\Config\IdempotencyConfig;

return new IdempotencyConfig(
    header: 'Idempotency-Key',
    requireKey: true,
    ttlInSeconds: 86_400,      // 24h default
    pendingTtlInSeconds: 60,   // 1min default
    cachePrefix: 'idempotency',
);
```

## Limitations

- Windows not supported (requires `pcntl_alarm`).
- Response bodies must be serializable; non-serializable (generators, views) stored as type strings.
