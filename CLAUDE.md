# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Incrudible is a Laravel package that provides an admin panel based on the Laravel + Inertia + React + TypeScript stack. It's inspired by Laravel Breeze and Backpack, but provides full control over both backend and frontend code through scaffolding rather than being a black-box solution.

Key characteristics:
- **Package Type**: Laravel package (installed via Composer: `dreammonkey/incrudible`)
- **Architecture**: Backend (Laravel) + Frontend (Inertia.js + React + TypeScript)
- **Installation Pattern**: Install package → Scaffold files into host application → Full control over generated code
- **Namespace**: All generated files use `App\Incrudible` by default (configurable in `config/incrudible.php`)

## Development Setup

### Local Package Development

To develop this package locally, create a blank Laravel project in a sibling directory and add this to its `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../incrudible"
    }
]
```

Then run: `composer require dreammonkey/incrudible @dev`

This allows you to work on the package while testing it in a real Laravel application.

## Essential Commands

### Testing
- `composer test` - Run Pest test suite
- `composer test-coverage` - Run tests with coverage report
- Run single test: `vendor/bin/pest tests/path/to/TestFile.php`
- Run specific test method: `vendor/bin/pest --filter "test name"`

### Code Quality
- `composer format` - Fix code style with Laravel Pint (Laravel preset with trailing commas)
- `composer analyse` - Run PHPStan static analysis (level 4)
- PHPStan checks Octane compatibility and model properties

### Package Commands (for host application)
- `php artisan incrudible:install` - Install package config and migrations
- `php artisan incrudible:scaffold` - Scaffold all admin panel files (controllers, models, views, etc.)
- `php artisan incrudible:admin` - Create admin user
- `php artisan make:crud {model}` - Generate complete CRUD for a model (backend + frontend)

## Architecture Overview

### Service Provider Pattern

The `IncrudibleServiceProvider` extends Spatie's `PackageServiceProvider` and uses traits to organize functionality:
- `RegistersAuthProvider` - Custom guard and authentication for admin panel
- `RegistersMiddleware` - Incrudible middleware group and aliases
- `RegistersRouteMacros` - Custom route macros (e.g., `Route::associate()` for many-to-many relations)

### CRUD Generation System

The `make:crud` command orchestrates multiple sub-commands to generate a complete CRUD:

1. **Config** (`crud:config`) - Creates `config/incrudible/{table}.php` with field definitions
2. **Model** (`crud:model`) - Generates model in `App\Incrudible\Models\`
3. **Resource** (`crud:resource`) - API resource for JSON transformation
4. **Controller** (`crud:controller`) - RESTful controller with Inertia responses
5. **Requests** (`crud:request`) - Form request validation classes (Index, Show, Store, Update, Destroy)
6. **Frontend** (`crud:frontend`) - React/TypeScript components (Index, Create, Edit, Show)

The system supports:
- **Nested resources**: Parent-child relationships (e.g., `/posts/{post}/comments`)
- **Relations**: Uses Laravel relationships and generates appropriate frontend components
- **Auto-validation**: Uses `laracraft-tech/laravel-schema-rules` to generate validation rules from database schema

### Stub System

The `stubs/` directory contains scaffoldable files that get copied into the host application:
- `stubs/app/Incrudible/` - Backend PHP files (Controllers, Models, Requests, etc.)
- `stubs/resources/js/Incrudible/` - Frontend TypeScript/React files
- `stubs/resources/css/` - Admin panel styles

When `incrudible:scaffold` runs, these files are copied to the host app where developers have full control to modify them.

### Authentication & Middleware

- **Guard**: Custom `incrudible` guard for admin authentication (separate from main app auth)
- **Model**: `App\Incrudible\Models\Admin` (configurable via `config/incrudible.php`)
- **Middleware Group**: `incrudible` middleware includes session, errors, Inertia handler, etc.
- **Helper Functions**:
  - `incrudible_guard_name()` - Returns guard name
  - `incrudible_middleware()` - Returns middleware key
  - `incrudible_route()` - Generate routes with admin prefix
  - `incrudible_user()` - Get authenticated admin

### Route Structure

Routes are defined in `routes/incrudible.php`:
- All routes prefixed with config value (default: `/incrudible`)
- Protected by `incrudible` middleware + `must-authenticate`
- Some routes require password confirmation (`must-confirm-password`)
- Custom `Route::associate()` macro for many-to-many relationship routes

### Frontend Architecture

React/TypeScript frontend using Inertia.js:
- **UI Components**: `stubs/resources/js/Incrudible/Components/` - Reusable components
- **Pages**: `stubs/resources/js/Incrudible/Pages/` - Inertia page components
- **Layouts**: `stubs/resources/js/Incrudible/Layouts/` - Page layouts
- **Types**: TypeScript definitions in `types/` directory
- **API Layer**: `Api/services/` for backend communication
- **Hooks**: React hooks in `Hooks/` directory
- **Context**: React context providers in `Context/`

### Configuration

Key config file: `config/incrudible.php`
- `namespace` - PHP namespace for generated files
- `route_prefix` - URL prefix for all admin routes
- `auth.user_model_fqn` - Admin model class
- `auth.middleware_classes` - Middleware stack
- `auth.guard` - Authentication guard name
- `menu.items` - Sidebar menu configuration

## Testing Approach

Uses Pest PHP with:
- `pestphp/pest-plugin-laravel` - Laravel-specific assertions
- `pestphp/pest-plugin-arch` - Architecture testing
- `orchestra/testbench` - Package testing environment

Tests are organized by feature:
- `tests/Admin/` - Admin functionality tests
- `tests/Auth/` - Authentication tests
- `tests/Commands/` - Artisan command tests

## Key Development Patterns

### Generating CRUDs

When generating CRUDs, the system:
1. Introspects database schema to determine field types
2. Generates validation rules using `GeneratesFormRules` trait
3. Creates both nested and non-nested resource controllers
4. Generates TypeScript interfaces and React components
5. Prompts for parent models if nested structure is needed

### Breeze-Style Helpers

The `BreezeHelpers` trait provides utilities similar to Laravel Breeze for scaffolding auth-related functionality.

### Publishing Pattern

Package uses Laravel's publish system:
- `--tag="incrudible-config"` - Publish CRUD config files
- `--tag="incrudible-routes"` - Publish routes file for customization
- `--tag="incrudible-migrations"` - Publish migrations

Once published, developers own these files and can modify freely.

## Important Notes

- **Static Analysis**: PHPStan baseline exists (`phpstan-baseline.neon`) for existing issues - new code should not add to baseline
- **Code Style**: Uses Laravel Pint with trailing commas enabled
- **Dependencies**: Requires PHP 8.1+ and Laravel 10 or 11
- **Frontend Build**: After scaffolding, run `npm ci && npm run dev` (or yarn equivalent)
- **Ziggy Routes**: Uses `tightenco/ziggy` to expose Laravel routes to JavaScript
- **Inertia**: Frontend uses Inertia.js for SPA-like experience without API
