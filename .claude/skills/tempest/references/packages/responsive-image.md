# Responsive Images (tempest/responsive-image)

**Source:** `vendor/tempest/framework/docs/3-packages/03-responsive-image.md`

Generates responsive image variants with `srcset`.

```sh
composer require tempest/responsive-image
```

## Usage

```php
use Tempest\ResponsiveImage\ResponsiveImageFactory;
use Tempest\ResponsiveImage\ResponsiveImageConfig;

$config = new ResponsiveImageConfig(
    srcPath: __DIR__ . '/resources/src',   // source images
    publicPath: __DIR__ . '/../public',    // output dir
    async: false,
    cache: true,
);

$imageFactory = new ResponsiveImageFactory($config);
$image = $imageFactory->create('/parrot.jpg');

echo $image->html;
// <img src="/parrot.jpg" srcset="/parrot-1920-1280.jpg 1920w, ...">
```

## With options

```php
use Tempest\ResponsiveImage\Size;

$image = $imageFactory->create(
    src: '/parrot.jpg',
    alt: 'A parrot',
    sizes: [new Size(maxWidth: 1000, width: 300), new Size(maxWidth: 1500, width: 500)],
    lazy: true,
);
```

## Config params

| Param | Description |
|-------|-------------|
| `srcPath` | Directory of source images |
| `publicPath` | Output directory (served by web server) |
| `async` | Generate variants in background (needs `tempest/command-bus`) |
| `cache` | Skip generation if public file already exists |
| `imageManager` | Intervention Image manager (customize driver) |

## Markdown integration

Used by `tempest/markdown` — pass `ResponsiveImageFactory` to `Markdown` constructor.
