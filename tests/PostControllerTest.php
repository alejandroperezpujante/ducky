<?php

declare(strict_types=1);

use App\Post\Post;
use App\Post\PostController;
use Tempest\DateTime\DateTime;

use function Tempest\Router\uri;

it('shows the create form', function () {
    $this->database->setup();

    $this->http->get('/posts/create')->assertOk()->assertSee('New Post');
});

it('creates a post', function () {
    $this->database->setup();

    $this->http->post('/posts', ['content' => 'Brand new content'])->assertRedirect();

    $this->database->assertTableHasRow('posts', content: 'Brand new content');
});

it('rejects empty content on create', function () {
    $this->database->setup();

    $this->http->post('/posts', ['content' => ''])->assertHasValidationError('content');
});

it('shows the edit form', function () {
    $this->database->setup();

    $post = Post::create(
        slug: 'edit-me',
        content: 'Original content',
        createdAt: DateTime::now(),
    );

    $this->http
        ->get(uri([PostController::class, 'edit'], post: $post->slug))
        ->assertOk()
        ->assertSee('Original content');
});

it('updates a post', function () {
    $this->database->setup();

    $post = Post::create(
        slug: 'update-me',
        content: 'Before update',
        createdAt: DateTime::now(),
    );

    $this->http
        ->patch(
            uri([PostController::class, 'update'], post: $post->slug),
            ['content' => 'After update'],
        )
        ->assertRedirect();

    $this->database->assertTableHasRow('posts', content: 'After update');
});

it('deletes a post', function () {
    $this->database->setup();

    $post = Post::create(
        slug: 'delete-me',
        content: 'To be deleted',
        createdAt: DateTime::now(),
    );

    $this->http->delete(uri([PostController::class, 'delete'], post: $post->slug))->assertRedirect();

    $this->database->assertTableDoesNotHaveRow('posts', content: 'To be deleted');
});
