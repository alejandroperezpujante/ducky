# Command Bus

**Source:** `vendor/tempest/framework/docs/2-features/10-command-bus.md`

## Commands

Plain PHP objects, no interface needed:

```php app/CreateUser.php
final readonly class CreateUser
{
    public function __construct(
        public string $name,
        public string $email,
        public string $passwordHash,
    ) {}
}
```

## Handlers

```php app/UserHandlers.php
use Tempest\CommandBus\CommandHandler;

final class UserHandlers
{
    #[CommandHandler]
    public function handleCreateUser(CreateUser $createUser): void
    {
        User::create(name: $createUser->name, email: $createUser->email);
    }
}
```

Command type inferred from first parameter type. Method name can be anything.

## Dispatch

```php
use function Tempest\command;
use Tempest\CommandBus\CommandBus;

command(new CreateUser($name, $email, $hash));  // function

// Or inject:
$this->commandBus->dispatch(new CreateUser($name, $email, $hash));
```

## Async commands

> **Experimental**

```php
use Tempest\CommandBus\Async;

#[Async]
final readonly class SendMail
{
    public function __construct(public string $to, public string $body) {}
}
```

Requires daemon: `./tempest command:monitor`

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
final readonly class ProcessPayment implements HasIdempotencyKey
{
    public function getIdempotencyKey(): string { return $this->paymentId; }
}
```

## Middleware

```php
use Tempest\CommandBus\CommandBusMiddleware;
use Tempest\CommandBus\CommandBusMiddlewareCallable;

class MyCommandBusMiddleware implements CommandBusMiddleware
{
    public function __invoke(object $command, CommandBusMiddlewareCallable $next): void
    {
        $next($command);
        if ($command instanceof ShouldBeLogged) {
            $this->logger->info($command->getLogMessage());
        }
    }
}
```

Priority: `#[Priority(Priority::HIGH)]`. Non-global: `#[SkipDiscovery]`.
