# Exception Handling

**Source:** `vendor/tempest/framework/docs/2-features/14-exception-handling.md`

## Process without throwing

```php
use Tempest\Core\Exceptions\ExceptionProcessor;

$this->exceptions->process($somethingFailed);  // reports without stopping execution
```

## Custom exception reporter

```php app/SentryExceptionReporter.php
use Tempest\Core\Exceptions\ExceptionReporter;

final class SentryExceptionReporter implements ExceptionReporter
{
    public function __construct(private SentryClient $sentry) {}

    public function report(Throwable $throwable): void
    {
        if (! $throwable instanceof CriticalException) return;  // conditional

        if ($throwable instanceof ProvidesContext) {
            // use $throwable->context() for extra data
        }

        $this->sentry->captureException($throwable);
    }
}
```

Auto-discovered. All registered reporters invoked per exception. Reporter exceptions caught silently.

## Exception context

```php
use Tempest\Core\ProvidesContext;

final readonly class UserWasNotFound extends \Exception implements ProvidesContext
{
    public function context(): array
    {
        return ['user_id' => $this->userId];
    }
}
```

## Custom exception renderer (HTTP responses)

```php app/NotFoundExceptionRenderer.php
use Tempest\Router\Exceptions\ExceptionRenderer;
use Tempest\Http\HttpRequestFailed;
use Tempest\Http\Status;

final class NotFoundExceptionRenderer implements ExceptionRenderer
{
    public function canRender(Throwable $throwable, Request $request): bool
    {
        return $request->accepts(ContentType::HTML)
            && $throwable instanceof HttpRequestFailed
            && $throwable->status === Status::NOT_FOUND;
    }

    public function render(Throwable $throwable): Response
    {
        return new NotFound(body: view('./404.view.php'));
    }
}
```

Checked in `#[Priority]` order. Auto-discovered.

## Disable logging reporter

```php app/exceptions.config.php
use Tempest\Core\Exceptions\ExceptionsConfig;
return new ExceptionsConfig(logging: false);
```

## Testing

```php
$this->exceptions->allowProcessing();  // default: disabled in tests
$this->exceptions->assertProcessed(UserNotFound::class);
$this->exceptions->assertNotProcessed(FooException::class);
$this->exceptions->assertNothingProcessed();
```
