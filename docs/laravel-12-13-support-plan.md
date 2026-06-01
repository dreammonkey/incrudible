# Laravel 12 And 13 Support Plan

Incrudible will ship the Laravel 12/13 work as a breaking major release. Laravel 10 and 11 become documented migration sources, not supported runtime targets.

## Target Matrix

Backend targets:

- Laravel 12.
- Laravel 13.
- PHP 8.2 or newer for Laravel 12.
- PHP 8.3 or newer for Laravel 13 if required by the Laravel 13 dependency graph.
- Test PHP 8.4 where the selected Laravel, Testbench, Pest, and Larastan versions support it.

Frontend targets:

- React 19.
- Inertia 3.
- Tailwind 4.
- Wayfinder.
- shadcn registry for versioned UI components.
- Vite 8.
- Kebab-case frontend folders and files.

Explicitly do not target Vite 7 for the new plan.

## Architecture

The PHP package remains responsible for:

- Backend package installation and configuration.
- Admin models, guards, middleware, routes, controllers, requests, resources, CRUD generators, and tests.
- Publishing backend config, migrations, and routes.
- Generating app-owned PHP CRUD code.
- Providing backend contracts/configuration required by generated frontend code.

Frontend delivery moves into two paths:

- `incrudible-starter-kit`
  - Recommended for greenfield applications.
  - Built on the Laravel React/Inertia starter-kit conventions.
  - Ships with Incrudible backend setup and frontend integration already in place.
- Existing app frontend installer
  - Command path for Laravel 12/13 apps that already use React/Inertia.
  - Installs Incrudible-owned frontend files without replacing the host app shell.
  - Uses registry components and small integration patches only where safe.

Both paths consume a versioned shadcn registry for Incrudible UI components.

## Existing App Frontend Installer

Keep `php artisan incrudible:install` backend-first.

Add or redesign `php artisan incrudible:frontend` for existing Laravel 12/13 React/Inertia projects.

The command should install only Incrudible-owned files:

```text
resources/js/incrudible/**
resources/css/incrudible.css
resources/js/types/incrudible.d.ts
```

Rules:

- Do not replace `resources/js/app.tsx`.
- Do not replace host layouts.
- Do not replace host auth pages.
- Do not replace host global components.
- Do not overwrite existing shadcn files in `resources/js/components/ui`.
- Detect Laravel starter-kit conventions and refuse destructive writes.
- Support `--dry-run` to list planned files and patches before writing.
- Support `--force` only for Incrudible-owned paths.
- Patch only small integration points when safe.
- Print manual instructions when automatic patching is unsafe.

Frontend ownership:

- Host app owns its normal application shell.
- Incrudible owns the admin shell under `resources/js/incrudible`.
- Shared shadcn primitives come from `resources/js/components/ui` when present.
- Incrudible-specific composites are registry components installed under `resources/js/incrudible/components`.
- Generated CRUD pages go under `resources/js/incrudible/pages`.
- Route calls use Wayfinder-generated modules, not Ziggy.

Safe integration points may include:

- Add an Incrudible Vite input if absent.
- Add expected Wayfinder route/action generation notes.
- Add TypeScript aliases only when they do not conflict with the starter kit.
- Emit import instructions instead of modifying ambiguous host files.

## Starter Kit Path

Use `../incrudible-starter-kit` for new applications.

The starter kit should:

- Start from the Laravel React/Inertia starter-kit structure.
- Include the Incrudible backend package setup.
- Include the Incrudible frontend already integrated.
- Use kebab-case paths.
- Use React 19, Inertia 3, Tailwind 4, Wayfinder, shadcn, and Vite 8.
- Treat the package as the backend/core source.
- Treat registry updates as the frontend UI update path.

The starter kit is recommended for greenfield projects, but existing projects must still have a non-destructive installer.

## Backend Work

Update `composer.json` for the new major release:

- Remove Laravel 10 and 11 support from runtime constraints.
- Add Laravel 12 and 13 support.
- Set PHP requirements according to the target Laravel matrix.
- Upgrade Testbench for Laravel 12 and 13.
- Upgrade Pest and Pest Laravel plugin.
- Upgrade Larastan/PHPStan and related extensions.
- Upgrade Pint and Collision.
- Remove `laracraft-tech/laravel-schema-rules`.
- Remove Ziggy as a modern frontend requirement unless a backend-only reason remains.

Replace schema-rule generation:

- Add an internal schema rule resolver.
- Use Laravel schema/database metadata.
- Cover nullability and required rules.
- Cover string max lengths.
- Cover integer, numeric, boolean, date, datetime, JSON, and text-like columns.
- Cover email field conventions, including unique email defaults.
- Cover password field conventions.
- Make the resolver configurable or replaceable through a contract binding.
- Add focused tests for rule generation and CRUD metadata output.

Preserve:

- Admin guard isolation.
- Middleware registration.
- Route prefix configuration.
- Built-in admins, roles, and permissions CRUDs.
- App-owned generated PHP code.

## CI Work

Update GitHub Actions:

- Test Laravel 12 and 13 only.
- Use compatible PHP versions for each Laravel version.
- Include PHP 8.4 where supported.
- Run package tests.
- Run static analysis.
- Keep code style checks.
- Avoid proving Laravel 10/11 compatibility in the new major branch.

Clean up Dependabot before feature implementation:

- Merge or close stale GitHub Actions update PRs.
- Update action versions in a controlled PR.
- Keep auto-merge limited to patch/minor updates after CI is green.
- Do not auto-merge major dependency updates.
- Add Composer dependency update coverage if it can be kept low-noise.

## Demo Work

Demo strategy:

- Keep Laravel 10/11 demos as migration references only.
- Recreate the Laravel 12 demo from a clean Laravel 12 React/Inertia starter kit.
- Use the Laravel 13 demo as the modern starter-kit convention reference.
- Use `../incrudible-starter-kit` as the greenfield validation target once populated.

Validation targets:

- Clean Laravel 12 demo install.
- Clean Laravel 13 starter-kit install.
- Existing Laravel 13 demo install using `php artisan incrudible:frontend`.

Each validation target should prove:

- Composer install/update succeeds.
- Backend install succeeds.
- Migrations run.
- Frontend dependencies install from a documented package list.
- `pnpm build` succeeds.
- `pnpm types:check` succeeds where configured.
- Admin login works.
- Dashboard works.
- Admins, roles, and permissions CRUDs work.
- One generated CRUD works.

## Phases

### Phase 1: Documentation

- Create `docs/current-state.md`.
- Create `docs/laravel-12-13-support-plan.md`.
- Create `docs/upgrade-guide.md`.
- Create `docs/dependency-pr-cleanup.md`.

No code behavior changes in this phase.

### Phase 2: Dependency And CI Baseline

- Update Composer constraints.
- Update CI matrix.
- Upgrade static analysis tooling.
- Clean up Dependabot/GitHub Actions state.
- Confirm package tests can run on the target matrix.

### Phase 3: Backend Compatibility

- Replace schema-rules with an internal resolver.
- Update tests around CRUD metadata and generated rules.
- Remove stale dependency bindings from tests.
- Confirm admin auth and built-in CRUD tests pass.

### Phase 4: Frontend Delivery

- Deprecate or narrow the destructive `incrudible:scaffold` behavior.
- Add `incrudible:frontend --dry-run`.
- Add non-destructive frontend install behavior.
- Move modern frontend paths to `resources/js/incrudible`.
- Replace Ziggy assumptions with Wayfinder imports.

### Phase 5: Starter Kit And Registry

- Populate `../incrudible-starter-kit`.
- Build a shadcn registry for Incrudible UI components.
- Make the starter kit consume the registry.
- Make existing app installer instructions consume the registry.

### Phase 6: Demo Validation

- Recreate Laravel 12 demo cleanly.
- Validate Laravel 13 starter-kit flow.
- Validate existing Laravel 13 app installer flow.
- Run browser smoke tests.

## Acceptance Checklist

- `composer test` passes.
- `composer analyse` passes.
- GitHub Actions passes for Laravel 12 and 13.
- PHP 8.4 is covered where the dependency matrix supports it.
- No runtime/dev dependency on `laracraft-tech/laravel-schema-rules`.
- Modern frontend path uses `resources/js/incrudible`.
- Modern frontend code uses Wayfinder, not Ziggy.
- Frontend target is Vite 8.
- Existing app installer does not overwrite host-owned frontend files.
- `--dry-run` reports files and patches before writing.
- `--force` only applies to Incrudible-owned files.
- Starter kit installs cleanly for a new app.
- Existing app frontend installer works in a Laravel 13 React/Inertia app.
- Upgrade guide covers Laravel 10/11 migrations, existing Laravel 12/13 apps, and new apps.
