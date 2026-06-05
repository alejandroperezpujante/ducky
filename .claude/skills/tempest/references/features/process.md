# Processes

**Source:** `vendor/tempest/framework/docs/2-features/16-process.md`

## Synchronous

```php
use Tempest\Process\ProcessExecutor;

final readonly class Composer
{
    public function __construct(private ProcessExecutor $executor) {}

    public function update(): void
    {
        $result = $this->executor->run('composer update');

        $result->successful(); // bool
        $result->failed();     // bool
        $result->exitCode;     // int
        $result->output;       // string
        $result->errorOutput;  // string
    }
}
```

## Asynchronous

```php
$process = $this->executor->start('composer update');

$process->wait(function (OutputChannel $channel, string $output) {
    echo $output;
});

$process->signal(SIGINT);
$process->stop();
```

## Process pools

```php
$pool = $this->executor->pool(['composer update', 'bun install']);
$pool->start();
$pool->count();
$pool->forEach(fn (InvokedProcess $p) => /* ... */);
$pool->signal(SIGINT);
$pool->stop();

// Or just get outputs:
[$composer, $bun] = $this->executor->concurrently(['composer update', 'bun install']);
```

## Testing

```php
// Mock results
$this->process->mockProcessResult('composer up');
$this->process->assertCommandRan('composer up');
$this->process->assertRan(fn (PendingProcess $p, ProcessResult $r) => /* ... */);

// Async mocks
$this->process->mockProcessResults([
    'composer up' => $this->process->describe()
        ->iterations(1)
        ->output('Nothing to install'),
]);

// Allow real processes
$this->process->allowRunningActualProcesses();
```

Default: processes not executed in tests (throws exception if not mocked).
