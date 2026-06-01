# Current State

This document captures the current Incrudible package shape before the Laravel 12/13 major-version work starts. It is intentionally descriptive, not an implementation plan.

## Package Philosophy

Incrudible is an Inertia, React, and TypeScript admin panel for Laravel applications. Its design is intentionally isolated from the host application:

- Backend admin code is generated into an `App\Incrudible` namespace.
- Frontend admin code is currently copied into `resources/js/Incrudible`.
- Admin authentication, guards, middleware, routes, controllers, requests, resources, models, and generated CRUD code are separate from the host application's normal user-facing code.
- Generated code is app-owned. After scaffolding or CRUD generation, the host app can edit the generated files directly.

This duplication has maintenance cost, but it is also the core product choice: Incrudible gives applications a fully owned admin surface instead of forcing all behavior through package internals.

## Current Package Structure

The package is based on the Spatie Laravel package skeleton and uses `spatie/laravel-package-tools`.

Important package areas:

- `src/IncrudibleServiceProvider.php`
  - Registers config, views, migrations, commands, auth provider setup, middleware aliases/groups, route macros, helpers, and route loading.
  - Publishes `routes/incrudible.php` with the `incrudible-routes` tag.
  - Publishes admin, role, and permission config files with the `incrudible-config` tag.
- `config/incrudible.php`
  - Defines the generated namespace, route prefix, guard name, middleware stack, and menu structure.
- `config/incrudible/*.php`
  - Contains built-in CRUD metadata for admins, roles, and permissions.
- `database/migrations/create_admins_table.php.stub`
  - Provides the package admin table migration.
- `routes/incrudible.php`
  - Defines the admin dashboard, profile, auth, admins, roles, permissions, settings, and relation routes.
- `src/Commands`
  - Contains `incrudible:scaffold`, `incrudible:admin`, `make:crud`, and lower-level CRUD generator commands.
- `src/Traits`
  - Contains route macro registration, auth provider registration, middleware registration, CRUD helper behavior, Breeze-style file helpers, and form-rule generation.
- `resources/stubs`
  - Contains PHP CRUD generator stubs and TSX page stubs.
- `stubs`
  - Contains the larger app scaffold copied by `incrudible:scaffold`.
- `tests`
  - Covers admin auth and built-in CRUD flows through Testbench.

## Composer Baseline

The current package targets Laravel 12 and 13:

- PHP: `^8.2`
- `illuminate/contracts`: `^12.0 || ^13.0`
- `inertiajs/inertia-laravel`: `^3.0`
- `laravel/wayfinder`: `^0.1`
- `spatie/laravel-package-tools`: `^1.16`

Current development dependencies include:

- `orchestra/testbench`: `^10.0 || ^11.0`
- `larastan/larastan`: `^3.0`
- `pestphp/pest`: `^3.0`
- `laravel/pint`: `^1.14`

For the next major release, Laravel 10 and 11 should become migration sources only.

## Install And Scaffold Flow

The current package has two separate installation ideas:

- `php artisan incrudible:install`
  - Provided through package-tools.
  - Publishes package config and migrations.
- `php artisan incrudible:scaffold`
  - Installs Composer packages for the host Laravel version.
  - Publishes Spatie permission assets.
  - Mutates `package.json`.
  - Copies backend app code into `app/Incrudible`.
  - Copies frontend code into `resources/js/Incrudible`.
  - Copies TypeScript, Vite, shadcn, bootstrap, and CSS files into host-owned root locations.
  - Generates Wayfinder routes/actions for the copied frontend.

The scaffold command follows an old Laravel Breeze-style approach. It is convenient for a blank app, but too destructive for modern Laravel starter-kit apps because it overwrites app-level frontend integration files.

## Backend CRUD Generation

`make:crud` orchestrates lower-level commands:

- `crud:config`
- `crud:model`
- `crud:resource`
- `crud:controller`
- `crud:request`
- `crud:frontend`

Generated backend files target the configured namespace, currently `App\Incrudible`. Generated frontend files currently target:

```text
resources/js/Incrudible/Pages/{Models}/{Component}.tsx
```

The CRUD config generator uses database schema metadata to create fields, searchable columns, sortable columns, and validation rules. It currently delegates rule generation to `laracraft-tech/laravel-schema-rules`.

## Schema Rule Generation

`src/Traits/GeneratesFormRules.php` depends on `LaracraftTech\LaravelSchemaRules\Contracts\SchemaRulesResolverInterface`.

The test harness manually binds the schema rules interface to the SQLite resolver. This confirms that rule generation is part of current CRUD generation behavior, but the implementation is coupled to an external package that is stale for the Laravel 12/13 target.

The next major version should replace this with an internal resolver that uses Laravel schema and database metadata directly.

## Frontend Baseline

The package frontend is currently copied from `stubs/resources/js/Incrudible`.

Current frontend traits:

- React 19.
- Inertia React 3.
- Tailwind 4.
- Vite 8.
- shadcn-style primitives copied into `resources/js/Incrudible/ui`.
- Wayfinder-generated route/action imports.
- CamelCased folder names such as `Incrudible`, `Pages`, `Components`, `Layouts`, `Hooks`, `Api`, and `Helpers`.
- Alias paths such as `@/Incrudible/Components/*` and `@/Incrudible/ui/*`.

The current Blade view loads:

```php
@vite(['resources/js/incrudible.tsx', "resources/js/Incrudible/Pages/{$page['component']}.tsx"])
```

This is a legacy shape for the Laravel 12/13 target. Modern frontend delivery should use kebab-case paths, Wayfinder-generated modules, Tailwind 4, React 19, Inertia 3, Vite 8, and a shadcn registry.

## Demo Repositories

The workspace contains several adjacent projects:

- `../laravel-10-incrudible-demo`
  - Legacy reference using Laravel 10, PHP `^8.1`, Inertia Laravel `^0.6.8`, Sanctum 3, Ziggy, schema-rules, and the older Incrudible scaffold.
- `../laravel-11-incrudible-demo`
  - Legacy reference using Laravel 11, PHP `^8.2`, Inertia Laravel `^1.0`, Sanctum 4, Ziggy, schema-rules, and the current package path repository.
- `../laravel-12-incrudible-demo`
  - Not a valid baseline.
  - Composer uses Laravel 12, Fortify, Inertia Laravel 2, Wayfinder, schema-rules, Ziggy, and the package path repository.
  - Frontend dependencies are mixed: `devDependencies` contain Inertia React 2, Vite 7, React 19, Tailwind 4, and Ziggy, while `dependencies` contain Inertia React 3, Vite 8, newer React 19, Tailwind 4, and newer Laravel Vite plugin versions.
  - This state came from broad dependency updating and copied legacy Incrudible files. Recreate this demo cleanly before using it for validation.
- `../laravel-13-incrudible-demo`
  - Fresh Laravel React/Inertia starter-kit style reference.
  - Composer uses Laravel 13, PHP `^8.3`, Inertia Laravel 3, Fortify, and Wayfinder.
  - Frontend uses React 19, Inertia React 3, Tailwind 4, shadcn-style components, and Vite 8.
  - It does not currently represent an installed Incrudible frontend.
- `../incrudible-starter-kit`
  - Reserved repository for the future Incrudible starter kit.
  - Currently empty except for Git metadata.
- `../laravel-schema-rules`
  - Local fork/reference for the external schema-rules package.
  - It should not remain a dependency of the next Incrudible major.

## CI And Tooling Baseline

Current GitHub Actions are still Laravel 10/11 focused:

- `run-tests.yml`
  - Tests Laravel `10.*` and `11.*`.
  - Tests PHP 8.2 and 8.3.
  - Uses Testbench 8 and 9.
  - Runs on Ubuntu and Windows.
- `phpstan.yml`
  - Runs on PHP 8.1.
  - Uses the current Composer dependency set.
- `dependabot.yml`
  - Tracks GitHub Actions only.
- `dependabot-auto-merge.yml`
  - Auto-merges Dependabot patch and minor updates when Dependabot opens the PR.
- `fix-php-code-style-issues.yml`
  - Runs Pint through a third-party action and auto-commits fixes.

The current PHPStan/Larastan stack is stale for the PHP 8.4 and Laravel 12/13 target and must be upgraded before static analysis can be treated as a release gate.

## Main Risks

- The current frontend scaffold overwrites too much host application frontend state.
- Legacy CamelCased frontend paths do not match Laravel starter-kit conventions.
- Ziggy is built into the existing frontend flow, while the target is Wayfinder.
- `laracraft-tech/laravel-schema-rules` blocks clean dependency modernization.
- The Laravel 12 demo cannot be used as proof because it contains a mixed dependency state.
- CI does not yet test the target Laravel versions.

## Direction

The backend package should remain the source of truth for admin core behavior and PHP code generation. Frontend delivery should split into:

- An `incrudible-starter-kit` path for new applications.
- A non-destructive `incrudible:frontend` installer path for existing Laravel 12/13 React/Inertia applications.
