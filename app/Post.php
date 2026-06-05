<?php

declare(strict_types=1);

namespace App;

use Tempest\Database\IsDatabaseModel;
use Tempest\DateTime\DateTime;
use Tempest\Router\Bindable;
use Tempest\Validation\Rules\HasLength;

use function Tempest\Database\query;
use function Tempest\Support\str;

final class Post implements Bindable
{
    use IsDatabaseModel;

    public string $slug;

    #[HasLength(min: 1)]
    public string $content;

    public DateTime $createdAt;
    public ?DateTime $updatedAt = null;

    public static function resolve(string $input): ?self
    {
        return query(self::class)->select()->where('slug = ?', $input)->first();
    }

    public static function generateSlug(): string
    {
        do {
            $slug = str()->random(10)->lower()->toString();
        } while (query(self::class)->select()->where('slug = ?', $slug)->first() !== null);

        return $slug;
    }
}
