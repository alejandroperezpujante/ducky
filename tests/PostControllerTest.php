<?php

declare(strict_types=1);

use App\Accounts\User;
use App\Posts\Post;
use App\Posts\PostController;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\DateTime\DateTime;

use function Tempest\Router\uri;

it('redirects unauthenticated user away from create form', function () {
    $this->database->setup();

    $this->http->get('/posts/create')->assertRedirect('/login');
});

it('shows the create form for authenticated user', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http->get('/posts/create')->assertOk()->assertSee('New Post');
});

it('redirects unauthenticated user away from post store', function () {
    $this->database->setup();

    $this->http
        ->post('/posts', ['content' => 'Brand new content'])
        ->assertRedirect('/login');
});

it('creates a post', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http
        ->post('/posts', ['content' => 'Brand new content'])
        ->assertRedirect();

    $this->database->assertTableHasRow('posts', content: 'Brand new content');
});

it('rejects empty content on create', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http
        ->post('/posts', ['content' => ''])
        ->assertHasValidationError('content');
});

it('shows the edit form for the owner', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $post = Post::create(
        slug: 'edit-me',
        content: 'Original content',
        author: $user,
        createdAt: DateTime::now(),
    );

    $this->http
        ->get(uri([PostController::class, 'edit'], post: $post->slug))
        ->assertOk()
        ->assertSee('Original content');
});

it('redirects non-owner away from edit form', function () {
    $this->database->setup();

    $owner = User::create(email: 'owner@ducky.test', password: 'password1234');
    $other = User::create(email: 'other@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($other);

    $post = Post::create(
        slug: 'edit-me',
        content: 'Original content',
        author: $owner,
        createdAt: DateTime::now(),
    );

    $this->http
        ->get(uri([PostController::class, 'edit'], post: $post->slug))
        ->assertRedirect('/');
});

it('updates a post', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $post = Post::create(
        slug: 'update-me',
        content: 'Before update',
        author: $user,
        createdAt: DateTime::now(),
    );

    $this->http
        ->patch(uri([PostController::class, 'update'], post: $post->slug), [
            'content' => 'After update',
        ])
        ->assertRedirect();

    $this->database->assertTableHasRow('posts', content: 'After update');
});

it('redirects non-owner away from update', function () {
    $this->database->setup();

    $owner = User::create(email: 'owner@ducky.test', password: 'password1234');
    $other = User::create(email: 'other@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($other);

    $post = Post::create(
        slug: 'update-me',
        content: 'Before update',
        author: $owner,
        createdAt: DateTime::now(),
    );

    $this->http
        ->patch(uri([PostController::class, 'update'], post: $post->slug), [
            'content' => 'Hijacked',
        ])
        ->assertRedirect('/');

    $this->database->assertTableHasRow('posts', content: 'Before update');
});

it('deletes a post', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $post = Post::create(
        slug: 'delete-me',
        content: 'To be deleted',
        author: $user,
        createdAt: DateTime::now(),
    );

    $this->http
        ->delete(uri([PostController::class, 'delete'], post: $post->slug))
        ->assertRedirect();

    $this->database->assertTableDoesNotHaveRow(
        'posts',
        content: 'To be deleted',
    );
});

it('redirects non-owner away from delete', function () {
    $this->database->setup();

    $owner = User::create(email: 'owner@ducky.test', password: 'password1234');
    $other = User::create(email: 'other@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($other);

    $post = Post::create(
        slug: 'delete-me',
        content: 'To be deleted',
        author: $owner,
        createdAt: DateTime::now(),
    );

    $this->http
        ->delete(uri([PostController::class, 'delete'], post: $post->slug))
        ->assertRedirect('/');

    $this->database->assertTableHasRow('posts', content: 'To be deleted');
});
