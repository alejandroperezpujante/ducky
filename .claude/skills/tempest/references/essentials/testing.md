# Testing

**Source:** `vendor/tempest/framework/docs/1-essentials/07-testing.md`

## Setup

Extend `IntegrationTest` (this project uses `Tests\IntegrationTestCase`):

```php tests/HomeControllerTest.php
use Tests\IntegrationTestCase;  // project wrapper
use PHPUnit\Framework\Attributes\Test;

final class HomeControllerTest extends IntegrationTestCase
{
    #[Test]
    public function it_renders_home(): void
    {
        $this->http->get('/')->assertOk()->assertSee('Welcome');
    }
}
```

Run: `./vendor/bin/phpunit` or `composer phpunit`.

## HTTP testing

```php
$this->http->get('/path')->assertOk()->assertSee('text');
$this->http->post('/path', ['field' => 'value'])->assertRedirect('/target');
$this->http->get('/api/users')->assertJson(['name' => 'Alice']);
```

## Database

```php
use PHPUnit\Framework\Attributes\PreCondition;  // runs after setUp()

final class MyControllerTest extends IntegrationTestCase
{
    #[PreCondition]
    protected function configure(): void
    {
        $this->database->setup();  // runs all migrations
    }
}

// Or specific migrations only:
$this->database->migrate(CreateMigrationsTable::class, CreateBookTable::class);
```

Dedicated test DB:
```php tests/database.testing.config.php
use Tempest\Database\Config\SQLiteConfig;
return new SQLiteConfig(path: __DIR__ . '/testing.sqlite');
```

## Console testing

```php
$this->console->call(ExportUsersCommand::class)->assertSuccess()->assertSee('12 exported');
$this->console->call(WipeCommand::class)->assertSee('caution')->submit()->assertSuccess();
```

## Event bus testing

```php
$this->eventBus->recordEventDispatches();
$this->eventBus->preventEventHandling();
$this->eventBus->assertDispatched(UserCreated::class);
$this->eventBus->assertDispatched(UserCreated::class, count: 2);
$this->eventBus->assertDispatched(
    event: UserCreated::class,
    callback: fn (UserCreated $e) => $e->email === 'test@example.com'
);
$this->eventBus->assertNotDispatched(UserDeleted::class);
```

## Mail testing

```php
$this->mailer->send(new WelcomeEmail($user))->assertSentTo($user->email)->assertAttached('welcome.pdf');
$this->mailer->shouldFail();  // simulate transport failure
$this->mailer->assertFailed(WelcomeEmail::class);
```

## Storage testing

```php
$storage = $this->storage->fake();
$storage->assertFileExists('profile.jpg');
$storage->assertEmpty();
$this->storage->preventUsageWithoutFake();
```

## Cache testing

```php
$cache = $this->cache->fake();
$cache->assertCached('users_count');
$cache->assertEmpty();
$cache->assertNotLocked('processing');
```

## Clock testing

```php
$clock = $this->clock();
$clock->setNow('2025-09-19 02:00:00');
$clock->sleep(milliseconds: 250);
```

## Exception testing

```php
$this->exceptions->allowProcessing();
$this->exceptions->assertProcessed(UserNotFound::class);
$this->exceptions->assertNotProcessed(FooException::class);
```

## Process testing

```php
$this->process->mockProcessResult('composer up');
$this->process->assertCommandRan('composer up');
$this->process->allowRunningActualProcesses();
```

## OAuth testing

```php
$oauth = $this->oauth->fake(new OAuthUser(id: 'jon', email: 'jon@test.com', nickname: 'jondoe'));
$oauth->assertAuthorizationUrlGenerated();
$oauth->assertUserFetched(code: 'fake-code');
```

## Environment spoofing

```php
use Tempest\Core\Environment;
$this->container->singleton(Environment::class, Environment::PRODUCTION);
```

## Discover test fixtures

Non-test files in `tests/` directory auto-discovered. Custom locations:

```php
protected function discoverTestLocations(): array
{
    return [new DiscoveryLocation('Tests\\Aircraft', __DIR__ . '/Aircraft')];
}
```
