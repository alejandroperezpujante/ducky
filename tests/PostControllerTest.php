<?php

declare(strict_types=1);

use App\Post;
use App\PostController;
use Tempest\DateTime\DateTime;

use function Tempest\Router\uri;

it('lists posts on the home page', function () {
    $this->database->setup();

    Post::create(
        slug: 'test-post',
        content: 'Hello from the test post',
        createdAt: DateTime::now(),
    );

    $this->http->get('/')->assertOk()->assertSee('Hello from the test post');
});

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

it('paginates posts on second page', function () {
    $this->database->setup();

    foreach (range(1, 12) as $i) {
        Post::create(
            slug: "post-{$i}",
            content: "Post number {$i}",
            createdAt: DateTime::now(),
        );
    }

    $this->http->get('/', query: ['page' => 2])->assertOk()->assertSee('Post number 11');
});
