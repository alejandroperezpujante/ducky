<?php

declare(strict_types=1);

namespace Tests;

use App\Book;
use PHPUnit\Framework\Attributes\Test;

final class BookTest extends IntegrationTestCase
{
    #[Test]
    public function it_persists_and_retrieves_a_book(): void
    {
        $this->database->setup();

        Book::create(title: 'Moby Dick');

        $this->database->assertTableHasRow('books', title: 'Moby Dick');
    }

    #[Test]
    public function it_returns_empty_table_after_reset(): void
    {
        $this->database->setup();

        $this->database->assertTableEmpty('books');
    }
}
