# Markdown (tempest/markdown)

**Source:** `vendor/tempest/framework/docs/3-packages/04-markdown.md`

Fast PHP Markdown parser — lexer-based (no regex), faster than league/commonmark.

```sh
composer require tempest/markdown
```

## Basic usage

```php
use Tempest\Markdown\Markdown;

$markdown = new Markdown();
$parsed = $markdown->parse(file_get_contents('README.md'));

echo $parsed->frontMatter['title'];
echo $parsed->html;
```

## Code highlighting

Powered by `tempest/highlight` (included):

```php
use Tempest\Highlight\Highlighter;

$markdown = new Markdown(
    highlighter: new Highlighter(/* configure theme */),
);

// Disable:
$markdown = new Markdown(highlighter: null);
```

## Responsive images

```php
use Tempest\ResponsiveImage\ResponsiveImageFactory;
use Tempest\ResponsiveImage\ResponsiveImageConfig;

$markdown = new Markdown(
    imageFactory: new ResponsiveImageFactory(new ResponsiveImageConfig(
        srcPath: __DIR__ . '/../resources/images',
        publicPath: __DIR__ . '/../public',
    )),
);
```

## View component

```html
<x-markdown># hi</x-markdown>
<x-markdown :content="$text" />
```
