# File Storage

**Source:** `vendor/tempest/framework/docs/2-features/05-file-storage.md`

Built on Flysystem.

## Config

```php app/s3.config.php
use Tempest\Storage\Config\S3StorageConfig;
use function Tempest\env;

return new S3StorageConfig(
    bucket: env('S3_BUCKET'),
    region: env('S3_REGION'),
    accessKeyId: env('S3_ACCESS_KEY_ID'),
    secretAccessKey: env('S3_SECRET_ACCESS_KEY'),
);
```

## Usage

```php
use Tempest\Storage\Storage;

final readonly class UserService
{
    public function __construct(private Storage $storage) {}

    public function getUrl(User $user): string
    {
        return $this->storage->publicUrl($user->profile_picture_path);
    }
}
```

Key methods:
```php
$storage->publicUrl($location);
$storage->write($location, $contents);
$storage->read($location);
$storage->delete($location);
$storage->fileOrDirectoryExists($location);
```

## Multiple storages

```php app/userdata.storage.config.php
return new S3StorageConfig(
    tag: StorageLocation::USER_DATA,
    bucket: env('USERDATA_S3_BUCKET'),
    // ...
);
```

```php
use Tempest\Container\Tag;

public function __construct(
    #[Tag(StorageLocation::BACKUPS)] private Storage $storage
) {}
```

## Available config classes

- `LocalStorageConfig`, `S3StorageConfig`, `R2StorageConfig`
- `AzureStorageConfig`, `FTPStorageConfig`, `SFTPStorageConfig`
- `GoogleCloudStorageConfig`, `InMemoryStorageConfig`, `ZipArchiveStorageConfig`
- `CustomStorageConfig` — any `FilesystemAdapter`

## Read-only storage

```php
return new S3StorageConfig(tag: ..., readonly: true, ...);
```
Requires: `composer require league/flysystem-read-only`

## Testing

```php
$storage = $this->storage->fake();            // in-memory, auto-cleared
$storage->assertFileExists('profile.jpg');
$storage->assertEmpty();

$this->storage->preventUsageWithoutFake();    // fail if non-faked storage used
```
