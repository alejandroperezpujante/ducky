# Asset Bundling (Vite)

**Source:** `vendor/tempest/framework/docs/2-features/02-asset-bundling.md`

## Quick start

```sh
php tempest install vite          # wizard: adds Vite plugin, vite.config.ts, entrypoints, optional Tailwind
npm run dev                       # start dev server
npm run build                     # production build
```

## Entrypoints

Files named `*.entrypoint.{ts,css,js}` are **auto-discovered** — no config needed.

```js app/main.entrypoint.ts
console.log('Hello, world!')
```

## View component

Add `<x-vite-tags />` to base layout:

```html app/ViewComponents/x-base.view.php
<html>
    <head>
        <x-vite-tags />    <!-- injects all discovered entrypoints -->
    </head>
    <body><x-slot /></body>
</html>
```

Specific entrypoint only:
```html
<x-vite-tags entrypoint="src/Profile/profile.css" />
```

## Manual entrypoint config

```php app/vite.config.php
return new ViteConfig(
    entrypoints: [
        'app/main.css',
        'app/main.ts',
    ],
);
```

## CSP nonce

```php
use Tempest\Vite\ViteConfig;
use Tempest\Support\Random;

final class ConfigureViteNonce implements HttpMiddleware
{
    public function __construct(private readonly ViteConfig $viteConfig) {}

    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        $this->viteConfig->nonce = Random\secure_string(length: 40);
        return $next($request);
    }
}
```

## Testing

```php
// Prevent ManifestNotFoundException in tests:
$this->vite->allowTagResolution();
```

## Gotchas

- `public/build/` directory should be in `.gitignore`
- `vite build` shells out to `php tempest vite:config` — PHP + `vendor/` must exist at build time.
- SRI: use `vite-plugin-manifest-sri` plugin; Tempest auto-reads hashes from `manifest.json`.
