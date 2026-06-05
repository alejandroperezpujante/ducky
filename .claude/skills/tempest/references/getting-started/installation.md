# Tempest Installation

**Source:** `vendor/tempest/framework/docs/0-getting-started/02-installation.md`

## Requirements

PHP 8.5+, Composer. Optional: Bun/Node for frontend.

## New project

```sh
composer create-project tempest/app my-app
cd my-app
php tempest serve           # built-in server on localhost:8000
php tempest install vite --tailwind  # optional frontend scaffolding
```

## Add to existing project

```sh
composer require tempest/framework
./vendor/bin/tempest install framework
# Installs: public/index.php, tempest (CLI), .env.example, .env
```

## Project structure

No forced structure. Both patterns work:

```
src/Authors/AuthorController.php     # Domain-first
src/Controllers/AuthorController.php # Layer-first
```

Tempest discovers everything via PSR-4 namespace scanning.

## Discovery

- Routes: `#[Get]`, `#[Post]` etc. on methods
- Console commands: `#[ConsoleCommand]` on methods
- View components: `x-*.view.php` files
- Entrypoints: `*.entrypoint.{ts,css,js}` files

After moving/renaming discoverable classes:
```sh
php tempest discovery:generate
# or composer dump-autoload (triggers same via post-autoload-dump hook)
```

## Disable discovery cache in dev (for vendor packages)

```env .env
DISCOVERY_CACHE=false
```
