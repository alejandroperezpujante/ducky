# Console Commands

**Source:** `vendor/tempest/framework/docs/1-essentials/04-console-commands.md`

## Basic command

```php app/TrackAircraft.php
use Tempest\Console\ConsoleCommand;
use Tempest\Console\HasConsole;

final readonly class TrackAircraft
{
    use HasConsole;  // provides $this->console

    #[ConsoleCommand(name: 'aircraft:track')]
    public function __invoke(AircraftType $type, ?int $radius = null): void
    {
        $this->console->info("Tracking {$type->value}");
    }
}
```

Auto-discovered — no registration needed.

## Console output

```php
$this->console->writeln('Hello!');
$this->console->info('Info message');
$this->console->error('Error message');
$this->console->warning('Warning');
$this->console->ask('What email?', validation: [new Email()]);
$this->console->confirm('Continue?');
$this->console->task('Syncing...', $this->synchronize(...));
$this->console->progressBar($items, fn ($item) => $this->process($item));
$this->console->search('Find book', $this->repository->find(...));
```

## Arguments

```php
#[ConsoleCommand('aircraft:track')]
public function __invoke(
    AircraftType $type,                   // required, enum-typed
    ?int $radius = null,                  // optional (nullable)
    bool $verbose = false,                // --verbose flag (--no-verbose to negate)
    #[ConsoleArgument(aliases: ['r'])]    // short alias -r
    ?string $format = null,
): void { ... }
```

## Command options

```php
#[ConsoleCommand(
    name: 'aircraft:track',
    description: 'Tracks operating aircraft',
    hidden: true,                         // hides from list
    aliases: ['track'],                   // ./tempest track
    middleware: [CautionMiddleware::class], // prevent prod usage
)]
```

## Exit codes

```php
use Tempest\Console\ExitCode;

public function __invoke(): ExitCode
{
    if (! $this->hasBeenSetup()) return ExitCode::ERROR;
    return ExitCode::SUCCESS;
}
```

## Middleware

```php app/InspireMiddleware.php
use Tempest\Console\ConsoleMiddleware;
use Tempest\Console\ConsoleMiddlewareCallable;

final readonly class InspireMiddleware implements ConsoleMiddleware
{
    public function __invoke(Invocation $invocation, ConsoleMiddlewareCallable $next): ExitCode|int
    {
        // $invocation->argumentBag->get('key')
        // $invocation->consoleCommand (the matched command attribute)
        return $next($invocation);
    }
}
```

Priority: `#[Priority(Priority::HIGH)]`. Non-global: `#[SkipDiscovery]`.

## Built-in middleware

- `CautionMiddleware` — prompts before running in production
- `ForceMiddleware` — adds `--force` flag to skip `confirm()` calls
- `HelpMiddleware` — provides `--help`

## Scheduling

```php
use Tempest\Console\Schedule;
use Tempest\Console\Scheduler\Every;

#[Schedule(Every::HOUR)]
#[ConsoleCommand('slack:update-channels')]
public function updateSlackChannels(): void { ... }
```

## Testing

```php
$this->console
    ->call(ExportUsersCommand::class)
    ->assertSuccess()
    ->assertSee('12 users exported');

$this->console
    ->call(WipeDatabaseCommand::class)
    ->assertSee('caution')
    ->submit()
    ->assertSuccess();
```

## Gotchas

- `HasConsole` trait uses `#[Inject]` to get `Console` — no constructor injection needed.
- Interactive components (search, progress bar) require Mac/Linux; Windows falls back to non-interactive.
- Inject CLI: `php tempest <name>` or `./tempest <name>`.
