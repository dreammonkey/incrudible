# Dependency And PR Cleanup

Before implementing Laravel 12/13 feature work, clean up dependency automation and CI so failures are meaningful.

## Current State

The package GitHub Actions are still Laravel 10/11 focused:

- `run-tests.yml`
  - Laravel matrix: `10.*`, `11.*`.
  - PHP matrix: 8.2, 8.3.
  - Testbench matrix: 8, 9.
  - Runs on Ubuntu and Windows.
- `phpstan.yml`
  - Runs on PHP 8.1.
  - Uses the current stale Composer dependency graph.
- `dependabot.yml`
  - Updates GitHub Actions only.
- `dependabot-auto-merge.yml`
  - Auto-merges Dependabot patch and minor updates.
- `fix-php-code-style-issues.yml`
  - Uses a Pint action and commits style fixes back to the branch.

The package Composer constraints are also pre-target:

- Runtime support is Laravel 10/11.
- Testbench support is Laravel 10/11.
- Larastan/PHPStan stack is stale for PHP 8.4.
- `laracraft-tech/laravel-schema-rules` remains in development dependencies.

## Goals

- Make CI represent the new Laravel 12/13 support policy.
- Make static analysis usable on the supported PHP versions.
- Reduce noisy dependency PRs before feature implementation.
- Keep automation helpful but conservative.
- Avoid broad major-version auto-merges.

## Cleanup Order

1. Review open Dependabot PRs.
2. Merge low-risk GitHub Actions patch/minor updates only after CI passes.
3. Close or supersede stale PRs that target the Laravel 10/11 branch assumptions.
4. Update workflow actions in one controlled PR if multiple Dependabot PRs overlap.
5. Update Composer constraints for the Laravel 12/13 major.
6. Update CI matrix for Laravel 12/13.
7. Upgrade PHPStan/Larastan and Pest/Testbench together.
8. Run tests and static analysis locally before relying on CI.

## GitHub Actions Plan

Update `run-tests.yml`:

- Remove Laravel 10 and 11 from the new major branch matrix.
- Add Laravel 12.
- Add Laravel 13.
- Use Testbench versions compatible with each Laravel version.
- Use PHP versions compatible with each Laravel version.
- Include PHP 8.4 where the dependency set supports it.
- Keep `prefer-stable`.
- Keep `prefer-lowest` only if dependency constraints can support it without false failures.

Update `phpstan.yml`:

- Run on a PHP version in the supported target matrix.
- Use upgraded Larastan/PHPStan versions.
- Run through the Composer script:

  ```bash
  composer analyse
  ```

Update code style workflow:

- Keep Pint.
- Prefer a workflow that reports or commits style changes predictably.
- Avoid hiding behavior changes inside style-only PRs.

## Dependabot Plan

Update `dependabot.yml`:

- Keep GitHub Actions updates.
- Consider adding Composer updates after the Laravel 12/13 dependency graph is stable.
- Avoid high-frequency frontend updates in the PHP package unless frontend registry/starter-kit ownership is decided.

Update `dependabot-auto-merge.yml`:

- Keep auto-merge limited to patch/minor updates.
- Require green CI before auto-merge.
- Do not auto-merge major updates.
- Do not auto-merge updates that touch the Laravel/Testbench/Pest/Larastan compatibility matrix.

## Static Analysis Notes

The current static analysis setup is not a valid release gate for the new target:

- `phpstan.yml` runs on PHP 8.1, below the Laravel 12/13 target.
- `larastan/larastan` is pinned to `^2.9`.
- PHPStan extensions are from the older stack.
- The plan requires PHP 8.4 coverage where supported.

Upgrade audit notes identify the current PHPStan/Larastan stack as a PHP 8.4 failure point. Treat static analysis as blocked until Larastan, PHPStan, extension packages, and Testbench are upgraded together.

## Composer Dependency Plan

Runtime dependencies for the new major:

- Require Laravel 12/13 compatible Illuminate packages.
- Require a PHP range compatible with Laravel 12 and 13.
- Keep package-tools if compatible.
- Remove Ziggy from the modern frontend path.
- Remove `laracraft-tech/laravel-schema-rules`.

Development dependencies:

- Upgrade Testbench for Laravel 12/13.
- Upgrade Pest and Pest Laravel plugin.
- Upgrade Larastan/PHPStan.
- Upgrade Pint.
- Upgrade Collision.
- Keep Spatie permission testing support compatible with the target Laravel versions.

## Verification

Run locally:

```bash
composer test
composer analyse
```

Then verify CI:

- Laravel 12 job passes.
- Laravel 13 job passes.
- Supported PHP versions pass.
- Static analysis passes.
- Dependabot auto-merge only activates after all required checks pass.

## Do Not Do

- Do not run broad frontend updates in legacy demos and treat the result as a valid baseline.
- Do not auto-merge major dependency updates.
- Do not keep Laravel 10/11 jobs as release gates for the new major.
- Do not treat PHPStan failures from the old Larastan stack as application failures.
- Do not begin frontend installer work before the dependency and CI baseline is trustworthy.
