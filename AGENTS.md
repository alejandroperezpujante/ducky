# AGENTS.md

This file provides guidance to AI models when working with code in this repository.

## Commands

```bash
# Dev server (PHP + Vite concurrently)
composer dev

# Tests
composer pest
vendor/bin/pest --filter "test name"             # single test by name
vendor/bin/pest tests/PostControllerTest.php     # single file

# Format / lint
composer fmt     # mago fmt (format only)
composer lint    # mago lint --fix --format-after-fix

# Full QA gate (fmt → pest → lint)
composer qa

# Console CLI
php tempest <command>                             # e.g. php tempest hello world

# Cache management (run after moving/renaming discoverable classes)
php tempest discovery:generate
php tempest cache:clear --all
```

## Architecture

### Discovery-driven wiring
Tempest scans `app/` (PSR-4 `App\`) and `vendor/` on boot — no central route table or command registry. To add something:
- **HTTP route**: create a class with `#[Get('/path')]` (or `#[Post]`, etc.) on a method — see `app/HomeController.php`.
- **Console command**: create a class with `#[ConsoleCommand]` on a method — see `app/HelloCommand.php`.

After renaming or moving a discoverable class, run `php tempest discovery:generate` (or `composer dump-autoload`, which triggers the same via `post-autoload-dump`).

### `app/` is flat and colocated
Controllers, commands, views, view components, and JS/CSS entrypoints all live directly under `app/`. No subdirectory convention is enforced; organize as needed.

### Views
Views are `*.view.php` files returned with `view('./relative/path.view.php')`. The base layout is `app/ViewComponents/x-base.view.php` — a standard HTML shell using `<x-slot>` (default body slot) and named slots (`head`, `scripts`). Views use `<x-base :title="...">` to render inside it.

### Frontend (Vite + Tailwind)
Entrypoint files follow the `app/*.entrypoint.ts` / `*.entrypoint.css` naming convention. `vite-plugin-tempest` discovers them automatically; assets are injected by `<x-vite-tags/>` in the layout. `vite build` shells out to `php tempest vite:config`, so PHP + `vendor/` must exist at build time.

### Tests
Tests use [Pest](https://pestphp.com/) with `it()/test()` closures. `$this->` in closures binds to `Tests\IntegrationTestCase` (wraps Tempest's `IntegrationTest`) via `tests/Pest.php`. Use `$this->http->get(...)` with fluent assertions (`assertOk`, `assertSee`). Test environment is configured in `phpunit.xml` (`ENVIRONMENT=testing`, `CACHE=null`).

### Docker deploy
`Dockerfile` has three stages: `build` (Composer + Node/Vite), `app` (php-fpm), `web` (nginx). `docker-compose.yml` wires `app:9000` → nginx. `deploy.sh` is the production deploy script (run on the server after SSH login: pull → build → cache:clear → discovery:generate → `docker compose up -d`). The container entrypoint (`docker/php/entrypoint.sh`) repeats cache:clear + discovery:generate on every boot. DB-migration and static-page steps are stubbed in both files — uncomment when needed.

### Formatter config
`mago.toml` sets 4-space indent, 180-column print width. Run `composer fmt` / `composer lint` rather than manually formatting — the CI QA gate runs both.
