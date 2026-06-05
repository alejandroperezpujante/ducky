# Configuration

**Source:** `vendor/tempest/framework/docs/1-essentials/06-configuration.md`

## Config files

Any `*.config.php` file anywhere in discoverable namespace — return a config object, auto-registered as singleton.

```php app/postgres.config.php
use Tempest\Database\Config\PostgresConfig;
use function Tempest\env;

return new PostgresConfig(
    host: env('DB_HOST'),
    port: env('DB_PORT'),
    username: env('DB_USERNAME'),
    password: env('DB_PASSWORD'),
    database: env('DB_DATABASE'),
);
```

## Inject config

```php
use Tempest\Core\AppConfig;

final readonly class AboutController
{
    public function __construct(private AppConfig $config) {}
}
```

## Custom config class

```php app/Slack/SlackConfig.php
final class SlackConfig
{
    public function __construct(
        public string $token,
        public string $baseUrl,
        public string $applicationId,
        public ?string $userAgent = null,
    ) {}
}
```

```php app/Slack/slack.config.php
use function Tempest\env;

return new SlackConfig(
    token: env('SLACK_API_TOKEN'),
    baseUrl: env('SLACK_BASE_URL', default: 'https://slack.com/api'),
    applicationId: env('SLACK_APP_ID'),
);
```

Then inject `SlackConfig` anywhere normally.

## Per-environment config

```php app/storage.prod.config.php
return new S3StorageConfig(
    bucket: env('S3_BUCKET'),
    region: env('S3_REGION'),
    // ...
);
```

Suffixes:
- Production: `.prd.`, `.prod.`, `.production.`
- Staging: `.stg.`, `.staging.`
- Development: `.dev.`, `.local.`
- Testing: `.test.`, `.testing.`

## Update config at runtime

```php
// Mutate property (singleton — persists for request lifecycle)
$this->viteConfig->nonce = Random\secure_string(length: 40);

// Replace entire config
$this->container->config(new SQLiteConfig(path: root_path('database.sqlite')));
```

## Cache env vars

```env .env
CONFIG_CACHE=true   # force enable (production auto-enables)
```
