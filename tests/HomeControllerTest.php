<?php

declare(strict_types=1);

use App\Post\Post;
use Tempest\DateTime\DateTime;

it('lists posts on the home page', function () {
    $this->database->setup();

    Post::create(
        slug: 'test-post',
        content: 'Hello from the test post',
        createdAt: DateTime::now(),
    );

    $this->http->get('/')->assertOk()->assertSee('Hello from the test post');
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
