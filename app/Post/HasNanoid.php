<?php

declare(strict_types=1);

namespace App\Post;

use ReflectionClass;

use function Tempest\Container\get;

/**
 * Augments any {@see \Tempest\Database\IsDatabaseModel} subclass with automatic nanoid generation.
 *
 * Declare the trait and resolve the `create()` conflict:
 * ```php
 * use IsDatabaseModel, HasNanoid {
 *     HasNanoid::create insteadof IsDatabaseModel;
 * }
 * ```
 *
 * Properties annotated with {@see Nanoid} that are not supplied by the caller will have
 * a nanoid injected automatically before the row is inserted.
 */
trait HasNanoid
{
    /**
     * Creates and persists the model, auto-filling any {@see Nanoid}-annotated properties
     * that were not explicitly provided.
     */
    public static function create(mixed ...$params): static
    {
        foreach (self::nanoidProperties() as $name => $size) {
            if (array_key_exists($name, $params)) {
                continue;
            }

            $params[$name] = get(NanoidGenerator::class)->generate($size);
        }

        return self::queryBuilder()->create(...$params);
    }

    /**
     * Returns a map of property name => nanoid size for all {@see Nanoid}-annotated properties.
     *
     * @return array<string, int>
     */
    private static function nanoidProperties(): array
    {
        $props = [];

        foreach (new ReflectionClass(static::class)->getProperties() as $prop) {
            $attrs = $prop->getAttributes(Nanoid::class);

            if ($attrs !== []) {
                $props[$prop->getName()] = $attrs[0]->newInstance()->size;
            }
        }

        return $props;
    }
}
