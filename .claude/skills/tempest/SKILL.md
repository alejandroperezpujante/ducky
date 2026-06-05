---
name: tempest
description: Write correct, up-to-date Tempest framework (PHP) code — routing, views, discovery, database, mapper, console commands, validation, auth, and more. Use for any task touching Tempest code in this project. Biases toward the bundled framework docs over pre-trained knowledge.
---

# Tempest Framework Skill

Pre-trained knowledge of Tempest may be outdated. **Always prefer the references below and the bundled docs at `vendor/tempest/framework/docs/` as source of truth.**

## Core Conventions

- **Discovery-driven**: no central route table, command registry, or DI config. Add `#[Get]`/`#[Post]`/`#[ConsoleCommand]`/`#[EventHandler]` attributes and Tempest finds them.
- **After moving/renaming a discoverable class**: run `php tempest discovery:generate` (or `composer dump-autoload`).
- **Views**: `*.view.php` files, returned with `view('./relative/path.view.php', key: $value)`.
- **View components**: any `x-*.view.php` file is auto-discovered as a component.
- **Config files**: `*.config.php` anywhere — return a config object, auto-registered as singleton.
- **Tests**: extend `Tests\IntegrationTestCase` (wraps `IntegrationTest`). Use `$this->http->get(...)` etc.
- **Entrypoints**: `*.entrypoint.ts` / `*.entrypoint.css` auto-discovered by Vite plugin.

## Reference Routing Table

| Topic | Load when... | Reference |
|-------|-------------|-----------|
| **Getting Started** | | |
| Introduction | Overview of Tempest philosophy | `references/getting-started/introduction.md` |
| Installation | Setting up new/existing projects | `references/getting-started/installation.md` |
| **Essentials** | | |
| Routing | HTTP routes, middleware, requests, responses, sessions | `references/essentials/routing.md` |
| Views | Templates, components, slots, directives, view objects | `references/essentials/views.md` |
| Database | ORM, query builder, models, migrations, seeders | `references/essentials/database.md` |
| Console commands | CLI commands, arguments, interactive components | `references/essentials/console-commands.md` |
| Container | DI, initializers, singletons, decorators, proxy | `references/essentials/container.md` |
| Discovery | How discovery works, custom discovery classes | `references/essentials/discovery.md` |
| Configuration | Config files, per-environment config | `references/essentials/configuration.md` |
| Testing | IntegrationTest, HTTP/console/DB testing utilities | `references/essentials/testing.md` |
| Primitive utilities | String/array helpers, `str()`, `arr()`, namespaced functions | `references/essentials/primitive-utilities.md` |
| **Features** | | |
| Mapper | `map()->to()`, `toJson()`, `MapFrom`, `Hidden`, custom mappers | `references/features/mapper.md` |
| Asset bundling | Vite integration, entrypoints, `<x-vite-tags />` | `references/features/asset-bundling.md` |
| Validation | Validator, validation rules, request validation | `references/features/validation.md` |
| Authentication | `Authenticatable`, `Authenticator`, access control policies | `references/features/authentication.md` |
| File storage | `Storage`, S3/R2/local config, multiple storages | `references/features/file-storage.md` |
| Cache | `Cache`, `resolve()`, locks, config, faking in tests | `references/features/cache.md` |
| Mail | `Mailer`, `Email`, `Envelope`, transports, testing | `references/features/mail.md` |
| Events | `EventBus`, `#[EventHandler]`, middleware, testing | `references/features/events.md` |
| Logging | `Logger`, channels, `DailyLogConfig`, `ll()`/`ld()` | `references/features/logging.md` |
| Command bus | `#[CommandHandler]`, `command()`, async, middleware | `references/features/command-bus.md` |
| Localization | `Translator`, translation files, MessageFormat 2.0 | `references/features/localization.md` |
| Scheduling | `#[Schedule]`, `Every`, `Interval`, cron setup | `references/features/scheduling.md` |
| HTTP client | (hidden/stub doc — use Symfony HttpClient directly) | `references/features/http-client.md` |
| Static pages | `#[StaticPage]`, `DataProvider`, `static:generate` | `references/features/static-pages.md` |
| Exception handling | `ExceptionReporter`, `ExceptionRenderer`, testing | `references/features/exception-handling.md` |
| DateTime | `DateTime::parse()`, `Duration`, `Clock`, timezones | `references/features/datetime.md` |
| Process | `ProcessExecutor`, `run()`, `start()`, pool, testing | `references/features/process.md` |
| OAuth | `OAuthClient`, provider configs, OAuth flow | `references/features/oauth.md` |
| TypeScript types | `#[AsType]`, `generate:typescript-types` | `references/features/typescript.md` |
| Idempotency | `#[Idempotent]`, routes + commands, scope resolver | `references/features/idempotency.md` |
| **Packages** | | |
| Highlight | `tempest/highlight`, syntax highlighting | `references/packages/highlight.md` |
| Console (standalone) | `tempest/console` as standalone package | `references/packages/console.md` |
| Responsive image | `tempest/responsive-image` | `references/packages/responsive-image.md` |
| Markdown | `tempest/markdown` parser | `references/packages/markdown.md` |
| **Internals** | | |
| Lifecycle | Boot sequence, `KernelEvent::BOOTED`, shutdown | `references/internals/lifecycle.md` |
| View spec | Technical view engine spec | `references/internals/view-spec.md` |
| **Extra** | | |
| Package development | Installers, `SkipDiscovery`, `KernelEvent::BOOTED` | `references/extra/package-development.md` |
| Standalone components | Using Tempest packages in isolation | `references/extra/standalone-components.md` |
| Deployments | Production deploy checklist, `.env` settings | `references/extra/deployments.md` |
