<?php

declare(strict_types=1);

namespace App\Concerns\Nanoid;

use Attribute;

/**
 * Marks a string property to automatically generate a nanoid value when the model is created.
 * The property should be declared `readonly`; the value is injected by {@see HasNanoid::create()}.
 *
 * **Example**
 * ```php
 * final class Post
 * {
 *     #[Nanoid]
 *     public readonly string $slug;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Nanoid
{
    public function __construct(public int $size = 21) {}
}
