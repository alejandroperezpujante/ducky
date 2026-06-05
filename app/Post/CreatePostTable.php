<?php

declare(strict_types=1);

namespace App\Post;

use Tempest\Database\MigratesDown;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;
use Tempest\Database\QueryStatements\DropTableStatement;

final class CreatePostTable implements MigratesUp, MigratesDown
{
    private(set) string $name = '2026-06-05_create_post_table';

    public function up(): QueryStatement
    {
        return CreateTableStatement::forModel(Post::class)
            ->primary()
            ->varchar('slug')
            ->text('content')
            ->datetime('createdAt')
            ->datetime('updatedAt', nullable: true)
            ->unique('slug');
    }

    public function down(): QueryStatement
    {
        return DropTableStatement::forModel(Post::class);
    }
}
