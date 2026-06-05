# Mapper

**Source:** `vendor/tempest/framework/docs/2-features/01-mapper.md`

## Basic mapping

```php
use function Tempest\Mapper\map;

$book = map($rawBookAsJson)->to(Book::class);
$books = map($rawBooksAsJson)->collection()->to(Book::class);
$array = map($book)->toArray();
$json = map($book)->toJson();
```

## Map from request to model (common pattern)

```php
$airport = map($request)->to(Airport::class)->save();
```

## Key attributes

```php
use Tempest\Mapper\MapFrom;  // map from a different source key
use Tempest\Mapper\MapTo;    // serialize to a different key
use Tempest\Mapper\Hidden;   // exclude from serialization + SELECT queries
use Tempest\Mapper\Strict;   // throw if properties missing

final class Book
{
    #[MapFrom('book_title')]
    public string $title;

    #[MapTo('book_title')]
    public string $titleForExport;

    #[Hidden]
    public string $password;
}

// Strict mapping — throws MappingValuesWereMissing on missing props
#[Strict]
final class StrictBook
{
    public string $title;
    public string $contents;
}
```

## Contexts

```php
$json = map($book)->in(SerializationContext::API)->toJson();
```

Custom serializer for context:
```php
use Tempest\Mapper\Attributes\Context;

#[Context(SerializationContext::API)]
final readonly class ApiDateSerializer implements Serializer, DynamicSerializer
{
    public static function accepts(PropertyReflector|TypeReflector $input): bool { ... }
    public function serialize(mixed $input): string { ... }
}
```

## Custom mappers

```php
use Tempest\Mapper\Mapper;

final readonly class PsrRequestToRequestMapper implements Mapper
{
    public function canMap(mixed $from, mixed $to): bool
    {
        return $from instanceof PsrRequest && is_a($to, Request::class, allow_string: true);
    }

    public function map(mixed $from, mixed $to): object { ... }
}
```

Auto-discovered via `MapperDiscovery`.

## Casters & serializers

```php
use Tempest\Mapper\Caster;
use Tempest\Mapper\Serializer;
use Tempest\Mapper\CastWith;
use Tempest\Mapper\SerializeWith;

final class AddressCaster implements Caster
{
    public function cast(mixed $input): Address { ... }
}

// Apply per property:
#[CastWith(AddressCaster::class)]
#[SerializeWith(AddressSerializer::class)]
public Address $address;
```

Global (dynamic) casters/serializers: implement `DynamicCaster`/`DynamicSerializer` with `accepts()` — auto-discovered.

## Explicit mapper selection

```php
map($request)->with(RequestToPsrRequestMapper::class)->do();
map($books)->collection()->with(ArrayToBooksMapper::class)->to(Book::class);
```
