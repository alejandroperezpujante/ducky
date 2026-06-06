<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Post\Post;
use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\Hashed;
use Tempest\Database\HasMany;
use Tempest\Database\IsDatabaseModel;
use Tempest\Database\PrimaryKey;
use Tempest\Mapper\Hidden;
use Tempest\Validation\Rules\IsEmail;

final class User implements Authenticatable
{
    use IsDatabaseModel;

    public PrimaryKey $id;

    #[IsEmail]
    public string $email;

    #[Hashed]
    #[Hidden]
    public string $password;

    /** @var \App\Post\Post[] */
    #[HasMany]
    public array $posts = [];
}
