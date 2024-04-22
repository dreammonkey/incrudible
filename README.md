# Incrudible

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dreammonkey/incrudible.svg?style=flat-square)](https://packagist.org/packages/dreammonkey/incrudible)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/dreammonkey/incrudible/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/dreammonkey/incrudible/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/dreammonkey/incrudible/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/dreammonkey/incrudible/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dreammonkey/incrudible.svg?style=flat-square)](https://packagist.org/packages/dreammonkey/incrudible)

Incrudible is yet another Admin panel that is based on the laravel inertia react typescript stack.
It borrows some behavior from Laravel Breeze scaffolding and some from Backpack.
Its setup is unique because it allows full control over the backend as well as over the frontend of the admin panel, this in contrast to some popular livewire / vue based admin panels.

## ROADMAP

-   Allow username or email for authentication
-   Admin roles integration (via spatie/laravel-permission)
-   Automatic CRUD generation
-   CRUD import and export support (via maatwebsite/excel)
-   Translations management integration.
-   Full fledged component library

## Installation

You can install the package via composer:

```bash
composer require dreammonkey/incrudible
php artisan incrudible:install
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="incrudible-migrations"
php artisan migrate
```

You can scaffold the admin panel code with:

```bash
php artisan incrudible:scaffold
# npm:
npm ci && npm run dev
# yarn:
yarn && yarn dev
```

## Usage

-   Create an admin user

```bash
php artisan incrudible:admin
```

Log in to the admin panel at https://localhost/incrudible

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Diederik van Remoortere](https://github.com/dreammonkey)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
