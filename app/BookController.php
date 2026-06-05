<?php

declare(strict_types=1);

namespace App;

use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class BookController
{
    #[Get('/books')]
    public function index(): View
    {
        return view('./books.view.php', books: Book::all());
    }
}
