<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Post\Post;
use SensitiveParameter;
use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\Hashed;
use Tempest\Database\HasMany;
use Tempest\Database\IsDatabaseModel;
use Tempest\Mapper\Hidden;
use Tempest\Validation\Rules\IsEmail;

final class User implements Authenticatable
{
    use IsDatabaseModel;

    public function __construct(
        #[IsEmail]
        public string $email,
        #[Hashed, Hidden, SensitiveParameter]
        public ?string $password,
        /** @var Post[] */
        #[HasMany]
        public array $posts = [],
    ) {}
}
