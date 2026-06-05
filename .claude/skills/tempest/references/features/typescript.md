# TypeScript Type Generation

**Source:** `vendor/tempest/framework/docs/2-features/18-typescript.md`

> **Experimental**

Generates TypeScript interfaces/types from PHP classes.

## Mark a class

```php
use Tempest\Generation\TypeScript\AsType;

#[AsType]
final class BookDTO
{
    public function __construct(
        public string $title,
        public string $author,
        public ?DateTime $publishedAt,
    ) {}
}
```

All app enums are included automatically without `#[AsType]`.

## Generate

```sh
./tempest generate:typescript-types
```

## Config

Default: single `types.d.ts` at project root, organized by namespace.

```php typescript.config.php
use Tempest\Generation\TypeScript\Writers\NamespacedTypeScriptGenerationConfig;
use Tempest\Generation\TypeScript\Writers\DirectoryTypeScriptGenerationConfig;

// Single file (default):
return new NamespacedTypeScriptGenerationConfig(filename: 'types.d.ts');

// Directory tree:
return new DirectoryTypeScriptGenerationConfig(directory: 'src/Web/types');
```

Add to `tsconfig.json`:
```json
{ "include": ["types.d.ts"] }
```

## Use in TypeScript

```ts
defineProps<{
    entry: Module.Changelog.ChangelogEntry
}>()
```

## Custom type resolver

```php
use Tempest\Generation\TypeScript\TypeResolver;

final class MyTypeResolver implements TypeResolver
{
    public function canResolve(TypeReflector $type): bool { ... }
    public function resolve(TypeReflector $type, TypeScriptGenerator $generator): TypeNode { ... }
}
```

Auto-discovered.
