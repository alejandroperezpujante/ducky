<?php

declare(strict_types=1);

use Tempest\Database\Config\SQLiteConfig;

use function Tempest\root_path;

return new SQLiteConfig(
    path: root_path('database/database.sqlite'),
);
