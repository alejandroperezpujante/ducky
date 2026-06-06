<?php

declare(strict_types=1);

namespace App\Posts;

use App\Accounts\User;
use Tempest\Auth\AccessControl\Policy;

final class PostPolicy
{
    #[Policy(Post::class)]
    public function create(?User $user): bool
    {
        return $user !== null;
    }

    #[Policy(Post::class, action: ['edit', 'update', 'delete'])]
    public function modify(Post $post, ?User $user): bool
    {
        return $user !== null && $post->author->id->value === $user->id->value;
    }
}
