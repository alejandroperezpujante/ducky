# Scheduling

**Source:** `vendor/tempest/framework/docs/2-features/11-scheduling.md`

## Define a schedule

```php app/ScheduledTasks.php
use Tempest\Console\Schedule;
use Tempest\Console\Scheduler\Every;
use Tempest\Console\Scheduler\Interval;

final readonly class ScheduledTasks
{
    #[Schedule(Every::HOUR)]
    public function updateSlackChannels(): void { ... }

    #[Schedule(new Interval(hours: 2, minutes: 30))]
    public function someTask(): void { ... }
}
```

`Every` enum values: `MINUTE`, `FIVE_MINUTES`, `FIFTEEN_MINUTES`, `THIRTY_MINUTES`, `HOUR`, `DAY`, `WEEK`, `MONTH`.

## Combined with console command

```php
#[Schedule(Every::HOUR)]
#[ConsoleCommand('slack:update-channels')]
public function updateSlackChannels(): void { ... }
```

## Production setup

One cron entry on the server:
```
0 * * * * user /path/to/tempest schedule:run
```

## Auto-discovered

No registration needed — `#[Schedule]` on any method is picked up by `ScheduleDiscovery`.
