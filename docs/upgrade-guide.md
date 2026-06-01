# Upgrade Guide

This guide describes the intended migration paths for the next Incrudible major release that supports Laravel 12 and 13 only.

The new frontend target is React 19, Inertia 3, Tailwind 4, Wayfinder, shadcn registry components, kebab-case paths, and Vite 8.

## New Applications

Use the Incrudible starter kit.

Recommended path:

1. Create the app from `incrudible-starter-kit`.
2. Configure the application environment.
3. Install PHP and frontend dependencies with the starter kit commands.
4. Run migrations.
5. Create the first admin.
6. Build assets.
7. Smoke test admin login, dashboard, admins, roles, and permissions.

Use package upgrades for backend/core changes.

Use registry updates for Incrudible UI component changes.

Do not start a new app by scaffolding legacy `resources/js/Incrudible/**` files into a generic Laravel app.

## Existing Laravel 12 Or 13 Applications

This path is for apps that already use Laravel 12 or 13 with React/Inertia.

Recommended path:

1. Install the PHP package.

   ```bash
   composer require dreammonkey/incrudible
   ```

2. Run backend installation.

   ```bash
   php artisan incrudible:install
   ```

3. Run migrations.

   ```bash
   php artisan migrate
   ```

4. Preview frontend installation.

   ```bash
   php artisan incrudible:frontend --dry-run
   ```

5. Review the planned files and patches.

6. Run the frontend installer.

   ```bash
   php artisan incrudible:frontend
   ```

7. Install required registry components.

8. Generate Wayfinder routes/actions.

9. Install documented frontend dependencies.

10. Run type checks and build.

    ```bash
    pnpm types:check
    pnpm build
    ```

11. Create or verify an admin account.

12. Smoke test the admin panel.

The frontend installer must be non-destructive. It should only install Incrudible-owned files under:

```text
resources/js/incrudible/**
resources/css/incrudible.css
resources/js/types/incrudible.d.ts
```

It must not replace:

- `resources/js/app.tsx`
- host application layouts
- host auth pages
- host global components
- existing shadcn primitives in `resources/js/components/ui`
- unrelated CSS or TypeScript config

If automatic patching is unsafe, follow the command's printed manual instructions instead of forcing overwrites.

## Laravel 10 Or 11 Applications

Laravel 10 and 11 are migration sources only for the new Incrudible major. Upgrade the host application first.

Recommended path:

1. Back up custom Incrudible changes.

2. Upgrade Laravel to 12 or 13 using Laravel's official upgrade path.

3. Upgrade Incrudible to the new major version.

4. Publish or review backend config and migrations.

5. Remove the old legacy frontend after backing up customizations.

   ```text
   resources/js/Incrudible/**
   resources/js/incrudible.tsx
   resources/css/incrudible.css
   ```

   Keep any custom code separately so it can be migrated into the new structure.

6. Install the modern frontend.

   ```bash
   php artisan incrudible:frontend --dry-run
   php artisan incrudible:frontend
   ```

7. Move custom admin frontend code into:

   ```text
   resources/js/incrudible/**
   ```

8. Rename legacy CamelCased paths to kebab-case.

   Examples:

   ```text
   resources/js/Incrudible/Pages/Admins/Index.tsx
   resources/js/incrudible/pages/admins/index.tsx

   resources/js/Incrudible/Components/IncrudibleForm.tsx
   resources/js/incrudible/components/incrudible-form.tsx

   resources/js/Incrudible/Hooks/use-incrudible.ts
   resources/js/incrudible/hooks/use-incrudible.ts
   ```

9. Replace Ziggy route usage with Wayfinder imports.

10. Reinstall frontend dependencies from the documented package list.

11. Do not run broad dependency upgrades such as:

    ```bash
    pnpm update --latest
    ```

    Broad updates can create an invalid mixed state across Inertia, Vite, Tailwind, shadcn, and Laravel starter-kit packages.

12. Run migrations.

13. Build frontend assets.

14. Smoke test admin CRUDs.

## Laravel 12 Demo Validation Notes

The existing `laravel-12-incrudible-demo` upgrade path was validated on 2026-05-05. The demo was not treated as a fresh install.

Required manual fixes during that validation:

- Upgrade the host app to `inertiajs/inertia-laravel:^3.0` and local Incrudible v2 with Composer `-W`.
- Restore app runtime packages that Composer may remove while resolving the v2 graph, specifically `spatie/laravel-permission` and `laravel/sanctum`.
- Ensure app-owned Incrudible PHP files exist before package discovery, then run `php artisan package:discover`.
- Add `@inertiajs/vite` to `vite.config.ts`, include `resources/css/incrudible.css` and `resources/js/incrudible.tsx` as Vite inputs, and enable the Inertia and Wayfinder Vite plugins.
- Run `php artisan wayfinder:generate` after the app routes boot.
- Install the scaffold frontend dependencies with `pnpm`, including `@inertiajs/vite`, `axios`, TanStack Query/Table, React Hook Form, Zod, date-fns, `react-input-mask`, `react-day-picker`, `cmdk`, Sonner, and the missing Radix primitives used by the scaffold.
- Keep the host app on the ESLint 9 / TypeScript 5 line until the Laravel starter-kit lint stack supports ESLint 10 cleanly.
- Exclude generated Wayfinder files and copied Incrudible scaffold files from the host starter-kit lint scope; keep `pnpm types:check` and `pnpm build` covering them.

Validation commands that passed for the upgraded demo:

```bash
php artisan package:discover
php artisan test
pnpm build
pnpm types:check
pnpm lint:check
```

## Legacy Frontend Migration Notes

The old frontend is based on:

- React 18.
- Inertia React 1.x.
- Tailwind 3.
- Vite 5.
- Ziggy.
- `resources/js/Incrudible/**`.

The modern frontend is based on:

- React 19.
- Inertia React 3.
- Tailwind 4.
- Vite 8.
- Wayfinder.
- shadcn registry components.
- `resources/js/incrudible/**`.

Migration rules:

- Treat host application shell files as host-owned.
- Treat the Incrudible admin shell as Incrudible-owned.
- Keep custom admin pages under `resources/js/incrudible/pages`.
- Keep custom admin components under `resources/js/incrudible/components`.
- Prefer host shadcn primitives from `resources/js/components/ui` when they exist.
- Put Incrudible-specific composites under `resources/js/incrudible/components`.
- Replace all Ziggy `route(...)` calls in modern frontend code with Wayfinder-generated modules.
- Keep generated CRUD pages inside the Incrudible namespace.

## Backend Migration Notes

The package continues to own:

- Admin guard and provider registration.
- Admin middleware.
- Incrudible route prefix and routes.
- Built-in admins, roles, and permissions CRUDs.
- CRUD generation commands.
- App-owned generated PHP code.

The new major removes the external schema-rules dependency. If an application customized schema-rule behavior through `laracraft-tech/laravel-schema-rules`, migrate that customization to the new Incrudible resolver contract/configuration once it is available.

## Verification

Run the relevant backend checks:

```bash
composer test
composer analyse
```

Run the relevant frontend checks:

```bash
pnpm types:check
pnpm build
pnpm lint:check
```

Smoke test:

- Admin login.
- Dashboard.
- Admins CRUD.
- Roles CRUD.
- Permissions CRUD.
- One generated CRUD.
