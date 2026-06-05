<?php

declare(strict_types=1);

namespace App;

use Tempest\Database\MigratesDown;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;
use Tempest\Database\QueryStatements\DropTableStatement;

final class CreateBookTable implements MigratesUp, MigratesDown
{
    private(set) string $name = '2026-06-05_create_book_table';

    public function up(): QueryStatement
    {
        return CreateTableStatement::forModel(Book::class)
            ->primary()
            ->text('title');
    }

    public function down(): QueryStatement
    {
        return DropTableStatement::forModel(Book::class);
    }
}
