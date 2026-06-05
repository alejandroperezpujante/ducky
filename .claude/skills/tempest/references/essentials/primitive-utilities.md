# Primitive Utilities

**Source:** `vendor/tempest/framework/docs/1-essentials/08-primitive-utilities.md`

## String utilities

Two styles: functional or OO (immutable preferred).

```php
use Tempest\Support\Str;
use function Tempest\Support\str;

// Functional (single operation)
$title = Str\to_title_case('my title');
$sentence = Str\to_sentence_case($input);

// OO (chaining)
$slug = str('/blog/01-chasing-bugs/')
    ->stripEnd('/')
    ->afterLast('/')
    ->replaceRegex('/\d+-/', '')
    ->slug()
    ->toString();

// Also: new ImmutableString(...) or new MutableString(...)
use Tempest\Support\Str\ImmutableString;
$s = new ImmutableString('hello world');
```

## Array utilities

```php
use Tempest\Support\Arr;
use function Tempest\Support\arr;

// Functional
$first = Arr\first($collection);

// OO
$items = arr(glob(__DIR__ . '/content/*.md'))
    ->reverse()
    ->map(fn (string $path) => /* ... */)
    ->mapTo(BlogPost::class);

// Also: new ImmutableArray(...) or new MutableArray(...)
```

## Namespaced function families

All under `Tempest\Support\*`:

```php
use Tempest\Support\Filesystem;
use Tempest\Support\Path;
use Tempest\Support\Json;
use Tempest\Support\Math;
use Tempest\Support\Regex;
use Tempest\Support\Random;

$contents = Filesystem\read_file(__DIR__ . '/content.md');
$path = Path\join($base, 'src', 'file.php');
$json = Json\encode(['key' => 'value']);
$rand = Random\secure_string(length: 40);
```

## Enum helpers

```php
use Tempest\Support\IsEnumHelper;

enum Status: string
{
    use IsEnumHelper;
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

Status::from('active');
Status::tryFrom('unknown'); // null
```

## Recommendation

Prefer `Str\*` / `Arr\*` functions for single operations; use OO chaining for multi-step transforms.
