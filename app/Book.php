<?php

declare(strict_types=1);

namespace App;

use Tempest\Database\IsDatabaseModel;

final class Book
{
    use IsDatabaseModel;

    public function __construct(
        public string $title,
    ) {}
}
