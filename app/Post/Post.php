<?php

declare(strict_types=1);

namespace App\Post;

use App\Authentication\User;
use App\Concerns\Nanoid\HasNanoid;
use App\Concerns\Nanoid\Nanoid;
use Tempest\Database\BelongsTo;
use Tempest\Database\IsDatabaseModel;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;
use Tempest\Mapper\Hidden;
use Tempest\Router\Bindable;
use Tempest\Router\IsBindingValue;
use Tempest\Validation\Rules\HasLength;
use Tempest\Validation\SkipValidation;

final class Post implements Bindable
{
    use IsDatabaseModel, HasNanoid {
        HasNanoid::create insteadof IsDatabaseModel;
    }

    #[IsBindingValue, SkipValidation, Hidden]
    public PrimaryKey $id;

    public function __construct(
        #[Nanoid]
        public string $slug,
        #[HasLength(min: 1)]
        public string $content,
        #[BelongsTo]
        public User $author,
        public DateTime $createdAt,
        public ?DateTime $updatedAt = null,
    ) {}

    public static function resolve(string $input): ?self
    {
        return self::find(slug: $input)
            ->with('author')
            ->first();
    }
}
