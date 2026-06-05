<?php

declare(strict_types=1);

namespace App\Post;

use Tempest\DateTime\DateTime;
use Tempest\Http\Request;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Delete;
use Tempest\Router\Get;
use Tempest\Router\Patch;
use Tempest\Router\Post as PostRoute;
use Tempest\View\View;

use function Tempest\Router\uri;
use function Tempest\View\view;

final readonly class PostController
{
    #[Get('/')]
    public function index(Request $request): View
    {
        $page = max(1, (int) ($request->get('page') ?? 1));
        $posts = Post::select()
            ->paginate(itemsPerPage: 10, currentPage: $page)
            ->map(
                static fn (Post $post) => [
                    'post' => $post,
                    'editUrl' => uri([self::class, 'edit'], post: $post->slug),
                    'deleteUrl' => uri(
                        [self::class, 'delete'],
                        post: $post->slug,
                    ),
                ],
            );

        return view('./post-index.view.php', posts: $posts);
    }

    #[Get('/posts/create')]
    public function create(): View
    {
        return view(
            './post-create.view.php',
            action: uri([self::class, 'store']),
        );
    }

    #[PostRoute('/posts')]
    public function store(StorePostRequest $request): Redirect|Back
    {
        Post::create(
            content: $request->content,
            createdAt: DateTime::now(),
        );

        return new Redirect(uri([self::class, 'index']));
    }

    #[Get('/posts/{post}/edit')]
    public function edit(Post $post): View
    {
        return view(
            './post-edit.view.php',
            post: $post,
            action: uri([self::class, 'update'], post: $post->slug),
            content: $post->content,
        );
    }

    #[Patch('/posts/{post}')]
    public function update(Post $post, StorePostRequest $request): Redirect|Back
    {
        $post->update(content: $request->content, updatedAt: DateTime::now());

        return new Redirect(uri([self::class, 'index']));
    }

    #[Delete('/posts/{post}')]
    public function delete(Post $post): Redirect
    {
        $post->delete();

        return new Redirect(uri([self::class, 'index']));
    }
}
