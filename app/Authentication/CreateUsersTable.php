<?php

declare(strict_types=1);

namespace App\Authentication;

use Tempest\Database\MigratesDown;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;
use Tempest\Database\QueryStatements\DropTableStatement;

final class CreateUsersTable implements MigratesUp, MigratesDown
{
    private(set) string $name = '2026-06-06_create_users_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('users')
            ->primary()
            ->varchar('publicId')
            ->varchar('email')
            ->varchar('password')
            ->unique('publicId')
            ->unique('email');
    }

    public function down(): QueryStatement
    {
        return new DropTableStatement('users');
    }
}
