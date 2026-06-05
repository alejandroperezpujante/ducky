# Database

**Source:** `vendor/tempest/framework/docs/1-essentials/03-database.md`

> **Experimental**: not covered by backwards compatibility promise.

## Config

Default: SQLite at `.tempest/database.sqlite`. Override with `*.config.php`:

```php app/Config/database.config.php
use Tempest\Database\Config\PostgresConfig;
use function Tempest\env;

return new PostgresConfig(
    host: env('DATABASE_HOST', default: '127.0.0.1'),
    port: env('DATABASE_PORT', default: '5432'),
    username: env('DATABASE_USERNAME', default: 'postgres'),
    password: env('DATABASE_PASSWORD', default: 'postgres'),
    database: env('DATABASE_DATABASE', default: 'postgres'),
);
```

Other configs: `SQLiteConfig`, `MysqlConfig`, `PostgresConfig`.

## Query builder

```php
use function Tempest\Database\query;

// With model class
query(Book::class)->select()->with('chapters', 'author')->where('id', $id)->first();
query(Book::class)->insert($book)->execute();
query(Book::class)->insert(title: 'Timeline Taxi')->execute();

// With table name
query('books')->select('id', 'title')->where('id = ?', $id)->first();
```

## Models

Plain PHP objects — no base class required:

```php app/Book.php
use Tempest\Validation\Rules\HasLength;

final class Book
{
    #[HasLength(min: 1, max: 120)]
    public string $title;

    public ?Author $author = null;        // BelongsTo (inferred)

    /** @var \App\Chapter[] */            // HasMany (inferred — MUST be fully qualified)
    public array $chapters = [];
}
```

## Active record: `IsDatabaseModel`

```php app/Book.php
use Tempest\Database\IsDatabaseModel;

final class Book
{
    use IsDatabaseModel;

    public string $title;
    public ?Author $author = null;
}

// Usage
$book = Book::create(title: 'Timeline Taxi', author: $author);
$books = Book::select()->with('author')->limit(10)->all();
$book->delete();
```

## Relations

Inferred from types. For non-default join columns, use attributes:

```php
use Tempest\Database\BelongsTo;
use Tempest\Database\HasMany;
use Tempest\Database\HasOne;

final class Book
{
    #[BelongsTo(ownerJoin: 'author_uuid', relationJoin: 'uuid')]
    public ?Author $author = null;

    /** @var \App\Chapter[] */
    #[HasMany(ownerJoin: 'chapter_uuid', relationJoin: 'uuid')]
    public array $chapters = [];
}
```

Also: `HasOneThrough`, `HasManyThrough`, `BelongsToMany` (pivot tables).

## Filtering by relations

```php
Author::select()->whereHas(relation: 'books')->all();
Author::select()->whereDoesntHave(relation: 'books')->all();
Author::select()->whereHas(relation: 'books', callback: fn (SelectQueryBuilder $q) => $q->whereField('published', true))->all();
```

## Table name

Inferred: pluralized snake_case classname. Override:

```php
use Tempest\Database\Table;

#[Table('table_books')]
final class Book { ... }
```

## UUIDs as primary keys

```php
use Tempest\Database\PrimaryKey;
use Tempest\Database\Uuid;

final class Book
{
    #[Uuid]
    public PrimaryKey $uuid;
}
```

Migration:
```php
->primary('uuid', uuid: true)
```

## Special properties

```php
use Tempest\Database\Hashed;       // hash on insert, detect re-hash
use Tempest\Database\Encrypted;    // encrypt/decrypt on serialize
use Tempest\Database\Virtual;      // computed, not in DB
use Tempest\Mapper\Hidden;         // exclude from SELECT (but included in INSERT/UPDATE)
```

## Migrations

```php app/CreateBooksTable.php
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatements\CreateTableStatement;

final class CreateBooksTable implements MigratesUp
{
    public string $name = '2024-08-12_create_books_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('books')
            ->primary()
            ->text('title')
            ->datetime('created_at')
            ->datetime('published_at', nullable: true)
            ->belongsTo('books.author_id', 'authors.id');
    }
}
```

Run: `./tempest migrate:up`  
Fresh: `./tempest migrate:fresh`  
Validate: `./tempest migrate:validate`

Down migrations: implement `MigratesDown` interface + `down()` returning `DropTableStatement`.

## Seeders

```php
use Tempest\Database\DatabaseSeeder;

final class BookSeeder implements DatabaseSeeder
{
    public function run(null|string|UnitEnum $database): void
    {
        query(Book::class)->insert(title: 'Timeline Taxi')->execute();
    }
}
```

Run: `./tempest database:seed` or `./tempest migrate:fresh --seed`

## Multiple databases

```php app/database.config.php
return new SQLiteConfig(path: __DIR__ . '/../database.sqlite', tag: 'main');
```

```php
// Inject tagged DB
#[Tag(DatabaseType::BACKUP)] private Database $backup

// Or via query
query(Book::class)->select()->onDatabase(DatabaseType::MAIN)->all();
```

## Gotchas

- Docblock types for `HasMany` **must** be fully qualified (`\App\Chapter[]`) — short names not supported via reflection.
- `IsDatabaseModel` adds auto-incrementing `$id` — incompatible with `#[Uuid]`.
- `#[Hashed]` requires `SIGNING_KEY` env var.
