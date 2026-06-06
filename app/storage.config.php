<?php

declare(strict_types=1);

use Tempest\Storage\Config\LocalStorageConfig;

use function Tempest\internal_storage_path;

return new LocalStorageConfig(path: internal_storage_path('avatars'));
