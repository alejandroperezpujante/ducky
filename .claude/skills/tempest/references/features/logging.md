# Logging

**Source:** `vendor/tempest/framework/docs/2-features/09-logging.md`

Built on Monolog, PSR-3, RFC 5424.

## Basic usage

```php
use Tempest\Log\Logger;

final readonly class UserService
{
    public function __construct(private Logger $logger) {}

    public function process(): void
    {
        $this->logger->info('Processing', ['user_id' => $id]);
        $this->logger->error('Failed', ['code' => $e->getCode()]);
        $this->logger->debug('Details');
        $this->logger->warning('Caution');
        $this->logger->critical('Critical error');
        $this->logger->emergency('System down');
    }
}
```

Default: daily rotating file at `.tempest/logs/`.

## Config

```php config/logging.config.php
use Tempest\Log\Config\DailyLogConfig;
use Tempest;

return new DailyLogConfig(
    path: Tempest\internal_storage_path('logs', 'tempest.log'),
    maxFiles: Tempest\env('LOG_MAX_FILES', default: 31),
);
```

## Multiple channels

```php config/logging.config.php
use Tempest\Log\Config\MultipleChannelsLogConfig;
use Tempest\Log\Channels\DailyLogChannel;
use Tempest\Log\Channels\SlackLogChannel;

return new MultipleChannelsLogConfig(channels: [
    new DailyLogChannel(
        path: Tempest\internal_storage_path('logs', 'app.log'),
        minimumLogLevel: LogLevel::DEBUG,
    ),
    new SlackLogChannel(
        webhookUrl: Tempest\env('SLACK_LOGGING_WEBHOOK_URL'),
        channelId: '#alerts',
        minimumLogLevel: LogLevel::CRITICAL,
    ),
]);
```

## Tagged loggers (multiple log destinations)

```php src/Orders/logging.config.php
return new DailyLogConfig(
    path: Tempest\internal_storage_path('logs', 'orders.log'),
    tag: Logging::ORDERS,
);
```

```php
public function __construct(
    #[Tag(Logging::ORDERS)] private Logger $logger
) {}
```

## Available channels

- `AppendLogChannel`, `DailyLogChannel`, `WeeklyLogChannel`
- `SlackLogChannel`, `SysLogChannel`

Config shortcuts: `SimpleLogConfig`, `DailyLogConfig`, `WeeklyLogConfig`, `SlackLogConfig`, `SysLogConfig`.

## Debug functions

```php
ll($var);   // write to debug log (no display)
lw($var);   // log + display (= dump())
ld($var);   // log + display + die (= dd())
le($var);   // log + emit ItemsDebugged event
```

Tail debug log: `./tempest tail:debug`  
Config: `debug.config.php` returning `DebugConfig(logPath: ...)`.
