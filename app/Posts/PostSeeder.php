<?php

declare(strict_types=1);

namespace App\Posts;

use App\Accounts\User;
use Tempest\Database\DatabaseSeeder;
use Tempest\DateTime\DateTime;
use UnitEnum;

final class PostSeeder implements DatabaseSeeder
{
    public function run(string|UnitEnum|null $database): void
    {
        if (Post::count()->execute() > 0) {
            return;
        }

        $demo = User::find(email: 'demo@ducky.test')->first();

        if ($demo === null) {
            return;
        }

        $samples = [
            'Tempest is a discovery-driven PHP framework — no config, just conventions.',
            'SQLite makes local development a breeze. Zero setup, full SQL.',
            'Tailwind CSS utility classes keep your views lean and readable.',
            'Vite hot-reloads your TypeScript and CSS in milliseconds.',
            'Route-model binding resolves a slug to a full model automatically.',
            'Validation rules live on the request class, not the controller.',
            'Migrations run on deploy; seeders run in dev only.',
            'The x-base layout component handles the full HTML shell.',
            'IsDatabaseModel gives you create(), save(), delete() for free.',
            'PaginatedData carries all pagination metadata you need for UI links.',
            'Named slots let components define multiple injection points.',
            'FormSession replays old input and validation errors on redirect.',
        ];

        foreach ($samples as $content) {
            Post::create(
                content: $content,
                author: $demo,
                createdAt: DateTime::now(),
            );
        }
    }
}
