# Highlight (tempest/highlight)

**Source:** `vendor/tempest/framework/docs/3-packages/01-highlight.md`

Standalone server-side syntax highlighter.

```sh
composer require tempest/highlight
```

## Basic usage

```php
use Tempest\Highlight\Highlighter;

$highlighter = new Highlighter();
$html = $highlighter->parse($code, 'php');
```

## Themes

CSS themes in `vendor/tempest/highlight/src/Themes/Css/`.

```css
@import "../vendor/tempest/highlight/src/Themes/Css/highlight-light-lite.css";
```

Inline theme (no CSS file needed):
```php
use Tempest\Highlight\Themes\InlineTheme;
$highlighter = new Highlighter(new InlineTheme(__DIR__ . '/solarized-dark.css'));
```

Terminal theme:
```php
use Tempest\Highlight\Themes\LightTerminalTheme;
$highlighter = new Highlighter(new LightTerminalTheme());
```

## Gutter

```php
$highlighter = new Highlighter()->withGutter(startAt: 10);
```

## Special tags (in code)

```
{_ emphasized _}    → .hl-em
{* strong *}        → .hl-strong
{~ blurred ~}       → .hl-blur
{+ addition +}      → .hl-addition
{- deletion -}      → .hl-deletion
{:classname: text :} → custom class
```

## CommonMark integration

```php
use Tempest\Highlight\CommonMark\HighlightExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new HighlightExtension());
$markdown = new MarkdownConverter($environment);
```

## Custom language

```php
final readonly class BladeLanguage extends HtmlLanguage
{
    public function getName(): string { return 'blade'; }
    public function getPatterns(): array
    {
        return [...parent::getPatterns(), new BladeKeywordPattern()];
    }
}
$highlighter->addLanguage(new BladeLanguage());
```
