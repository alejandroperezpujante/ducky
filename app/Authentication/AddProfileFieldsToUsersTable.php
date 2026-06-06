<?php

declare(strict_types=1);

namespace App\Authentication;

use Tempest\Database\MigratesDown;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\AlterTableStatement;
use Tempest\Database\QueryStatements\CompoundStatement;
use Tempest\Database\QueryStatements\UniqueStatement;
use Tempest\Database\QueryStatements\VarcharStatement;

final class AddProfileFieldsToUsersTable implements MigratesUp, MigratesDown
{
    private(set) string $name = '2026-06-07_add_profile_fields_to_users_table';

    public function up(): QueryStatement
    {
        return new CompoundStatement(
            new AlterTableStatement('users')->add(new VarcharStatement('username', nullable: true)),
            new AlterTableStatement('users')->add(new VarcharStatement('displayName', nullable: true)),
            new AlterTableStatement('users')->add(new VarcharStatement('avatarPath', nullable: true)),
            new UniqueStatement('users', ['username']),
        );
    }

    public function down(): QueryStatement
    {
        return new CompoundStatement(
            new AlterTableStatement('users')->dropColumn('username'),
            new AlterTableStatement('users')->dropColumn('displayName'),
            new AlterTableStatement('users')->dropColumn('avatarPath'),
        );
    }
}
