# View Technical Specification

**Source:** `vendor/tempest/framework/docs/4-internals/02-view-spec.md`

Technical reference for how Tempest View compiles templates.

## Expression attributes (`:attr`)

Attributes starting with `:` = PHP code:

```html
<div :if="$condition"></div>
<x-component :title="$content->title"></x-component>
```

Escape for frontend frameworks: `::attr` → `::if="frontend-code"` renders as `:if`.

## Control structures → PHP

```html
<!-- Compiles to: -->
<div :if="$a">A</div>
<div :elseif="$b">B</div>
<div :else>C</div>

<!-- <?php if($a) { ?><div>A</div><?php } elseif($b) { ?><div>B</div><?php } else { ?><div>C</div><?php } ?> -->
```

```html
<div :foreach="$items as $k => $v">...</div>
<div :forelse>Empty</div>

<!-- Compiles to: if(iterator_count($items)) foreach ... else ... -->
```

Combined: `<div :foreach="..." :if="$k !== 0">` — parsed in order.

## Echoing

```html
{{ strtoupper($var) }}         <!-- escaped -->
{!! $markdown->render($c) !!}  <!-- raw -->
{{-- comment --}}               <!-- stripped server-side -->
<!-- HTML comment → sent to browser -->
```

## Imports merged at top

```html
<?php
use App\PostController;
use function Tempest\Router\uri;
?>
{{ uri([PostController::class, 'show'], post: $post->id) }}
```

All imports from view files merged into top of compiled output.

## View file resolution order

1. Exact path (absolute paths with `__DIR__`)
2. Relative to controller's location
3. Search all discovery locations

Must end with `.view.php`.

## View objects

```php
use Tempest\View\View;
use Tempest\View\IsView;

final class BookView implements View
{
    use IsView;

    public function __construct(
        public string $title,
        public Book $book,
    ) {
        $this->path = __DIR__ . '/books.view.php';
    }

    public function summarize(Book $book): string { ... }
}
```

In view: `$this->summarize($book)` — public methods accessible via `$this`.

## Component scope

Components are closed scopes — like PHP closures. Must pass variables explicitly. View-defined data (from controller) is available as local variables.

## Boolean attributes

```html
<option :value="$v" :selected="$selected">  <!-- $selected = true → renders selected, false → omits -->
<div :data-active="{$isActive}"></div>
```
