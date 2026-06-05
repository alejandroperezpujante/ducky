# Cache

**Source:** `vendor/tempest/framework/docs/2-features/06-cache.md`

## Usage

```php
use Tempest\Cache\Cache;
use Tempest\DateTime\Duration;

final readonly class OrderService
{
    public function __construct(private Cache $cache) {}

    public function getCount(): int
    {
        return $this->cache->resolve(
            key: 'orders_count',
            callback: fn () => $this->fetchFromDb(),
            expiration: Duration::hours(12)
        );
    }
}
```

Key methods:
```php
$cache->get($key);
$cache->put($key, $value);
$cache->resolve($key, fn () => $expensiveOp(), expiration: Duration::minutes(30));
```

## Clear cache

```sh
./tempest cache:clear  # clears main cache (prompts for named caches)
```

## Locks

```php
$lock = $cache->lock('processing', Duration::seconds(30));

if ($lock->acquire()) {
    $this->process();
    $lock->release();
}

// Or with callback:
$lock->execute($this->process(...), wait: Duration::seconds(30));

// Known owner:
$cache->lock("processing:{$processId}", owner: $processId)->release();
```

## Config

```php app/cache.config.php
use Tempest\Cache\Config\FilesystemCacheConfig;

return new FilesystemCacheConfig();
```

Available: `FilesystemCacheConfig`, `InMemoryCacheConfig`, `PhpCacheConfig`.

## Env vars

```env .env
CACHE_ENABLED=false         # disable project cache
CACHE_CUSTOM_ENABLED=false  # disable named cache "custom"
INTERNAL_CACHES=false       # disable all internal caches
VIEW_CACHE=false
DISCOVERY_CACHE=false
CONFIG_CACHE=false
ICON_CACHE=false
```

## Testing

```php
$cache = $this->cache->fake();
$cache->assertCached('users_count');
$cache->assertEmpty();
$cache->assertNotLocked('processing');
```
