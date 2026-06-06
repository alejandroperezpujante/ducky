<?php

declare(strict_types=1);

namespace App\Home;

use App\Post\Post;
use App\Post\PostController;
use Tempest\Http\Request;
use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\Router\uri;
use function Tempest\View\view;

final readonly class HomeController
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
                    'editUrl' => uri([PostController::class, 'edit'], post: $post->slug),
                    'deleteUrl' => uri(
                        [PostController::class, 'delete'],
                        post: $post->slug,
                    ),
                ],
            );

        return view('./home.view.php', posts: $posts);
    }
}
