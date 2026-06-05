<?php

declare(strict_types=1);

namespace App\Post;

use Tempest\Database\IsDatabaseModel;
use Tempest\DateTime\DateTime;
use Tempest\Router\Bindable;
use Tempest\Validation\Rules\HasLength;

use function Tempest\Database\query;

final class Post implements Bindable
{
    use IsDatabaseModel, HasNanoid {
        HasNanoid::create insteadof IsDatabaseModel;
    }

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
