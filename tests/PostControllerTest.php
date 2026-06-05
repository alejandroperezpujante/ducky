<?php

declare(strict_types=1);

namespace Tests;

use App\Post;
use App\PostController;
use PHPUnit\Framework\Attributes\Test;
use Tempest\DateTime\DateTime;

use function Tempest\Router\uri;

final class PostControllerTest extends IntegrationTestCase
{
    #[Test]
    public function it_lists_posts_on_the_home_page(): void
    {
        $this->database->setup();

        Post::create(
            slug: 'test-post',
            content: 'Hello from the test post',
            createdAt: DateTime::now(),
        );

        $this->http->get('/')->assertOk()->assertSee('Hello from the test post');
    }

    #[Test]
    public function it_shows_the_create_form(): void
    {
        $this->database->setup();

        $this->http->get('/posts/create')->assertOk()->assertSee('New Post');
    }

    #[Test]
    public function it_creates_a_post(): void
    {
        $this->database->setup();

        $this->http->post('/posts', ['content' => 'Brand new content'])->assertRedirect();

        $this->database->assertTableHasRow('posts', content: 'Brand new content');
    }

    #[Test]
    public function it_rejects_empty_content_on_create(): void
    {
        $this->database->setup();

        $this->http->post('/posts', ['content' => ''])->assertHasValidationError('content');
    }

    #[Test]
    public function it_shows_the_edit_form(): void
    {
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
    }

    #[Test]
    public function it_updates_a_post(): void
    {
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
    }

    #[Test]
    public function it_deletes_a_post(): void
    {
        $this->database->setup();

        $post = Post::create(
            slug: 'delete-me',
            content: 'To be deleted',
            createdAt: DateTime::now(),
        );

        $this->http->delete(uri([PostController::class, 'delete'], post: $post->slug))->assertRedirect();

        $this->database->assertTableDoesNotHaveRow('posts', content: 'To be deleted');
    }

    #[Test]
    public function it_paginates_posts_on_second_page(): void
    {
        $this->database->setup();

        foreach (range(1, 12) as $i) {
            Post::create(
                slug: "post-{$i}",
                content: "Post number {$i}",
                createdAt: DateTime::now(),
            );
        }

        $this->http->get('/', query: ['page' => 2])->assertOk()->assertSee('Post number 11');
    }
}
