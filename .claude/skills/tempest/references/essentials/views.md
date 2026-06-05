# Views

**Source:** `vendor/tempest/framework/docs/1-essentials/02-views.md`

## Return a view from a controller

```php app/AircraftController.php
use Tempest\Router\Get;
use Tempest\View\View;
use function Tempest\View\view;

final readonly class AircraftController
{
    #[Get(uri: '/aircraft/{aircraft}')]
    public function show(Aircraft $aircraft): View
    {
        return view('./show.view.php', aircraft: $aircraft);
    }
}
```

Path forms (all equivalent):
```php
return view(__DIR__ . '/views/home.view.php');
return view('./views/home.view.php');
return view('views/home.view.php');
```

## View objects

```php app/AircraftView.php
use Tempest\View\View;
use Tempest\View\IsView;
use function Tempest\root_path;

final class AircraftView implements View
{
    use IsView;

    public function __construct(
        public Aircraft $aircraft,
        public AircraftType $type,
    ) {
        $this->path = root_path('src/Aircraft/aircraft.view.php');
    }
}
```

Add `@var` type hint in view file for IDE autocompletion:
```html
<?php /** @var \App\AircraftView $this */ ?>
<p :if="$this->type === AircraftType::PC24">...</p>
```

## Template syntax

```html
{{-- server-side comment (not rendered) --}}
{{ $escaped }}           {{-- escaped output --}}
{!! $rawHtml !!}         {{-- raw/unescaped output --}}

<!-- control flow directives -->
<div :if="$condition">...</div>
<div :elseif="$other">...</div>
<div :else>...</div>
<h1 :isset="$title">{{ $title }}</h1>

<li :foreach="$items as $item">{{ $item->name }}</li>
<li :forelse>No items.</li>

<!-- expression attributes (evaluated as PHP) -->
<html :lang="$this->user->language">
<option :value="$v" :selected="$selected">  <!-- boolean attribute -->

<!-- wrapper without DOM element -->
<x-template :foreach="$posts as $post">
    <div>{{ $post->title }}</div>
</x-template>
```

## View components

Create `x-*.view.php` anywhere — auto-discovered.

```html app/x-base.view.php
<html lang="en">
    <head><title :if="$title ?? null">{{ $title }}</title></head>
    <body><x-slot /></body>
</html>
```

Use:
```html app/home.view.php
<x-base :title="$this->post->title">
    <article>{{ $this->post->body }}</article>
</x-base>
```

**Attribute casing**: `kebab-case` → `$camelCase` variable, `camelCase`/`PascalCase` → `$lowercase`.
Idiomatic: always use `kebab-case` attributes.

## Slots

```html x-base.view.php
<head><x-slot name="styles" /></head>
<body><x-slot /></body>  <!-- default slot -->
```

```html home.view.php
<x-base>
    <x-slot name="styles"><style>...</style></x-slot>
    <p>Body content → default slot</p>
</x-base>
```

Default slot content:
```html x-my-component.view.php
<div>
    <x-slot>Fallback</x-slot>
    <x-slot name="header">Fallback header</x-slot>
</div>
```

Dynamic slots:
```html x-tabs.view.php
<div :foreach="$slots as $slot">
    <h1>{{ $slot->name }}</h1>
    <p>{!! $slot->content !!}</p>
</div>
```

## Built-in components

```html
<x-form :action="uri(StorePostController::class)">
    <x-input name="title" />
    <x-input name="content" type="textarea" />
    <x-input type="email" name="email" />
    <x-submit label="Save" />
</x-form>

<x-csrf-token />
<x-icon name="tabler:x" class="size-4" />
<x-markdown># Hello</x-markdown>
<x-markdown :content="$text" />
<x-vite-tags />  <!-- injects all discovered entrypoints -->
```

Install vendor components:
```sh
./tempest install view-components
```

## View processors (pre-process before render)

```php
use Tempest\View\View;
use Tempest\View\ViewProcessor;

final class StarCountViewProcessor implements ViewProcessor
{
    public function process(View $view): View
    {
        if (! $view instanceof WithStargazersCount) return $view;
        return $view->data(stargazers: $this->github->getStarCount());
    }
}
```

## View caching (production)

```env .env
VIEW_CACHE=true
```

Clear on deploy: `./tempest view:clear`

## Response processors (post-process before send)

```php
use Tempest\Router\ResponseProcessor;

final readonly class ErrorResponseProcessor implements ResponseProcessor
{
    public function process(Response $response): Response
    {
        if (! $response->status->isSuccessful()) {
            return $response->setBody(view('./error.view.php', status: $response->status));
        }
        return $response;
    }
}
```

## Gotchas

- Views live anywhere — but must be in a PSR-4 namespace directory for discovery.
- Component scope is closed: pass variables explicitly with expression attributes.
- `{!! $raw !!}` — only use on trusted/sanitized data (XSS risk).
