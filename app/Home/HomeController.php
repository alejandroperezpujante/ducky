<?php

declare(strict_types=1);

namespace App\Home;

use App\Post\Post;
use App\Post\PostController;
use Tempest\Auth\AccessControl\AccessControl;
use Tempest\Http\Request;
use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\Router\uri;
use function Tempest\View\view;

final readonly class HomeController
{
    public function __construct(
        private AccessControl $accessControl,
    ) {}

    #[Get('/')]
    public function index(Request $request): View
    {
        $page = max(1, (int) ($request->get('page') ?? 1));
        $posts = Post::select()
            ->with('author')
            ->paginate(itemsPerPage: 10, currentPage: $page)
            ->map(
                fn (Post $post) => [
                    'post' => $post,
                    'editUrl' => uri([PostController::class, 'edit'], post: $post->slug),
                    'deleteUrl' => uri(
                        [PostController::class, 'delete'],
                        post: $post->slug,
                    ),
                    'canModify' => $this->accessControl->isGranted('edit', $post)->granted,
                ],
            );

        return view('./home.view.php', posts: $posts);
    }
}
