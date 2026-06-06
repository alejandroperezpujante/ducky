<?php

declare(strict_types=1);

namespace App\Accounts;

use Tempest\Database\DatabaseSeeder;
use UnitEnum;

final class UserSeeder implements DatabaseSeeder
{
    public function run(string|UnitEnum|null $database): void
    {
        if (User::count()->execute() > 0) {
            return;
        }

        User::create(
            email: 'demo@ducky.test',
            password: 'password',
            username: 'demo',
        );
    }
}
