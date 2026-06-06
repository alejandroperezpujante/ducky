<?php

declare(strict_types=1);

namespace App\Post;

use Tempest\Database\IsDatabaseModel;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;
use Tempest\Mapper\Hidden;
use Tempest\Router\Bindable;
use Tempest\Router\IsBindingValue;
use Tempest\Validation\Rules\HasLength;
use Tempest\Validation\SkipValidation;

use function Tempest\Database\query;

final class Post implements Bindable
{
    use IsDatabaseModel, HasNanoid {
        HasNanoid::create insteadof IsDatabaseModel;
    }

    #[IsBindingValue, SkipValidation, Hidden]
    public PrimaryKey $id;

    #[Nanoid]
    public readonly string $slug;

    #[HasLength(min: 1)]
    public string $content;

    public DateTime $createdAt;
    public ?DateTime $updatedAt = null;

    public static function resolve(string $input): ?self
    {
        return query(self::class)->select()->where('slug = ?', $input)->first();
    }
}
