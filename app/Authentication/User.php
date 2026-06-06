<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Concerns\Nanoid\HasNanoid;
use App\Concerns\Nanoid\Nanoid;
use App\Post\Post;
use SensitiveParameter;
use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\Hashed;
use Tempest\Database\HasMany;
use Tempest\Database\IsDatabaseModel;
use Tempest\Database\PrimaryKey;
use Tempest\Mapper\Hidden;
use Tempest\Router\Bindable;
use Tempest\Router\IsBindingValue;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\SkipValidation;

final class User implements Authenticatable, Bindable
{
    use IsDatabaseModel, HasNanoid {
        HasNanoid::create insteadof IsDatabaseModel;
    }

    #[IsBindingValue, SkipValidation, Hidden]
    public PrimaryKey $id;

    public function __construct(
        #[Nanoid]
        public string $publicId,
        #[IsEmail]
        public string $email,
        #[Hashed, Hidden, SensitiveParameter]
        public ?string $password,
        public ?string $username = null,
        public ?string $displayName = null,
        public ?string $avatarPath = null,
        /** @var Post[] */
        #[HasMany]
        public array $posts = [],
    ) {}

    public function name(): string
    {
        return $this->displayName ?? $this->username ?? '';
    }

    public static function resolve(string $input): ?self
    {
        return self::find(publicId: $input)->first();
    }
}
