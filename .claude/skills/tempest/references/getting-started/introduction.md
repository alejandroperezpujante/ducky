# Tempest Introduction

Discovery-driven PHP framework. No central route table, no manual DI registration. Attributes on methods = automatic registration.

**Source:** `vendor/tempest/framework/docs/0-getting-started/01-introduction.md`

## Core philosophy

- **Discovery**: scans `app/` (PSR-4 `App\`) and `vendor/` on boot. Route attributes → routes, `#[ConsoleCommand]` → CLI commands, `#[EventHandler]` → event handlers. No explicit registration.
- **No forced structure**: flat, DDD, hexagonal — all work without config.
- **Modern PHP**: PHP 8.5+, property hooks, attributes, proxy objects.

## Key patterns

```php
// Route: attribute on any class method
final readonly class HomeController
{
    #[Get(uri: '/home')]
    public function __invoke(): View
    {
        return view('./home.view.php');
    }
}

// Console command: same pattern
final readonly class RssSyncCommand
{
    #[ConsoleCommand('rss:sync')]
    public function __invoke(bool $force = false): void {}
}

// Model: plain PHP object, no base class
final class Book
{
    public string $title;
    public ?Author $author = null;
    /** @var \App\Chapter[] */
    public array $chapters = [];
}

// Query
$book = query(Book::class)->select()->with('author')->where('id', $id)->first();

// View template (superset of HTML)
// <x-base :title="$this->seo->title">
//   <li :foreach="$this->books as $book">{{ $book->title }}</li>
// </x-base>
```

## Discovery cache

Production: always cached. Development: auto-discovered app code; vendor code needs `discovery:generate` after `composer update`.

```bash
php tempest discovery:generate  # regenerate after moving discoverable classes
php tempest cache:clear --all    # full cache wipe
```
