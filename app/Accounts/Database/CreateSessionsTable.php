<?php

declare(strict_types=1);

namespace App\Accounts\Database;

use Tempest\Database\MigratesDown;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;
use Tempest\Database\QueryStatements\DropTableStatement;

final class CreateSessionsTable implements MigratesUp, MigratesDown
{
    private(set) string $name = '2026-06-07_create_sessions_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('sessions')
            ->uuid('id')
            ->datetime('created_at')
            ->datetime('last_active_at')
            ->text('data');
    }

    public function down(): QueryStatement
    {
        return new DropTableStatement('sessions');
    }
}
