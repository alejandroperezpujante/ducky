<?php

declare(strict_types=1);

namespace App;

use Tempest\Database\DatabaseSeeder;
use UnitEnum;

final class BookSeeder implements DatabaseSeeder
{
    public function run(string|UnitEnum|null $database): void
    {
        if (Book::count()->execute() > 0) {
            return;
        }

        foreach (['The Pragmatic Programmer', 'Clean Code', 'Refactoring', 'Domain-Driven Design'] as $title) {
            Book::create(title: $title);
        }
    }
}
