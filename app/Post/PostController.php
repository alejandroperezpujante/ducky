<?php

declare(strict_types=1);

namespace App\Post;

use App\Authentication\MustBeAuthenticated;
use App\Authentication\User;
use App\Home\HomeController;
use Tempest\Auth\AccessControl\AccessControl;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\DateTime\DateTime;
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
    public function __construct(
        private Authenticator $authenticator,
        private AccessControl $accessControl,
    ) {}

    #[Get('/posts/create', middleware: [MustBeAuthenticated::class])]
    public function create(): View
    {
        return view(
            './post-create.view.php',
            action: uri([self::class, 'store']),
        );
    }

    #[PostRoute('/posts', middleware: [MustBeAuthenticated::class])]
    public function store(StorePostRequest $request): Redirect|Back
    {
        /** @var User $user */
        $user = $this->authenticator->current();

        Post::create(
            content: $request->content,
            author: $user,
            createdAt: DateTime::now(),
        );

        return new Redirect(uri([HomeController::class, 'index']));
    }

    #[Get('/posts/{post}/edit', middleware: [MustBeAuthenticated::class])]
    public function edit(Post $post): Redirect|View
    {
        if (! $this->accessControl->isGranted('edit', $post)->granted) {
            return new Redirect(uri([HomeController::class, 'index']));
        }

        return view(
            './post-edit.view.php',
            post: $post,
            action: uri([self::class, 'update'], post: $post->slug),
            content: $post->content,
        );
    }

    #[Patch('/posts/{post}', middleware: [MustBeAuthenticated::class])]
    public function update(Post $post, StorePostRequest $request): Redirect|Back
    {
        if (! $this->accessControl->isGranted('update', $post)->granted) {
            return new Redirect(uri([HomeController::class, 'index']));
        }

        $post->update(content: $request->content, updatedAt: DateTime::now());

        return new Redirect(uri([HomeController::class, 'index']));
    }

    #[Delete('/posts/{post}', middleware: [MustBeAuthenticated::class])]
    public function delete(Post $post): Redirect
    {
        if (! $this->accessControl->isGranted('delete', $post)->granted) {
            return new Redirect(uri([HomeController::class, 'index']));
        }

        $post->delete();

        return new Redirect(uri([HomeController::class, 'index']));
    }
}
