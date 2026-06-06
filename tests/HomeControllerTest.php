<?php

declare(strict_types=1);

use App\Authentication\User;
use App\Post\Post;
use Tempest\DateTime\DateTime;

it('lists posts on the home page', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');

    Post::create(
        slug: 'test-post',
        content: 'Hello from the test post',
        author: $user,
        createdAt: DateTime::now(),
    );

    $this->http->get('/')->assertOk()->assertSee('Hello from the test post');
});

it('paginates posts on second page', function () {
    $this->database->setup();

    $user = User::create(email: 'a@ducky.test', password: 'password1234');

    foreach (range(1, 12) as $i) {
        Post::create(
            slug: "post-{$i}",
            content: "Post number {$i}",
            author: $user,
            createdAt: DateTime::now(),
        );
    }

    $this->http->get('/', query: ['page' => 2])->assertOk()->assertSee('Post number 11');
});
