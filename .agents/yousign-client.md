# Yousign Client - Project-Specific Guide

Project-specific patterns for the Yousign client library. Combine with the team-wide standards in
[php-agents.md](./php-agents.md) and the package conventions in [laravel-package-setup.md](./laravel-package-setup.md).

---

## Project Overview

A standalone Laravel **package** (`coverzen/yousign-client`) that wraps the [Yousign](https://yousign.com) e-signature
REST API (v3). It is installed as a Composer dependency in consuming Laravel apps; it is **not** a runnable application.

- **PHP Version**: 8.3
- **Laravel**: 12 / 13 (`illuminate/support: ^12.0 || ^13.0`)
- **Package Type**: SOA client library (no controllers, no routes, no database tables)

All code lives under a `v1/` namespace segment (`Libs/Soa/v1`, `Structs/Soa/v1`, `Enums/v1`, `Facades/v1`,
`Fakes/v1`, `database/factories/v1`). This `v1` tracks the **Yousign API version**, not the package version — a future
API version is added as a parallel `v2/` tree, never by mutating `v1`.

---

## Development Commands

This project uses **DDEV**. Prefix all PHP/Composer commands with `ddev` — never run them in the local shell.

### Testing

```bash
ddev composer test                                          # run the full Pest suite
ddev exec ./vendor/bin/pest tests/Unit/Libs/Soa/v1/YousignTest.php   # single file
ddev exec ./vendor/bin/pest --filter='it_initiates_a_procedure'      # single test by name
```

### Code Quality & Analysis

```bash
ddev composer analyse            # run ALL quality checks (what CI runs) — must pass before commit
ddev composer php-cs-fixer-fix   # auto-fix code style
ddev composer phpcbf             # auto-fix coding standards

# Individual tools
ddev composer php-cs-fixer       # code style (dry-run)
ddev composer phpcs              # PHP CodeSniffer
ddev composer phpstan            # static analysis (level 10)
ddev composer psalm              # Psalm
ddev composer phpmd              # Mess Detector
ddev composer phpmnd             # Magic Number Detector
ddev composer phpinsights        # code insights
```

`composer analyse` chains, in order: php-cs-fixer → phpcs → phpinsights → phpmd → phpmnd (src + tests) → phpstan
(level 10) → psalm. **All tools must pass** before committing. Before adding `@phpstan-ignore` / `@psalm-suppress`
inline, check `phpstan.neon` / `psalm.xml` — there may already be a scoped global exclusion; ask before adding new ones.

---

## Architecture

The request flow has four cooperating layers:

**1. Facade** (`src/Facades/v1/Yousign.php`)
- Public entry point. `getFacadeAccessor()` resolves to the SOA lib, so `Yousign::initiateSignature(...)` calls through
  to it.
- The `@method` docblock is the authoritative list of available operations — keep it in sync when adding methods.
- `Yousign::fake()` swaps the container binding for `YousignFaker` (see Testing).

**2. SOA Library** (`src/Libs/Soa/v1/Yousign.php`, extends abstract `Soa`)
- The only layer that performs HTTP. The constructor builds one configured `Http` `PendingRequest`: base URL, `Bearer`
  auth, timeouts, retry with **exponential backoff**, and `->throw()` that logs failures via `Soa::logClientFailure`.
- Each public method maps to one endpoint, sends a request Struct's payload, and hydrates the JSON response into a
  response Struct.
- Retries only fire on `ConnectionException` (`Soa::manageRetry`); backoff is `2^(attempt-1) * retry_sleep`
  (`Soa::calculateBackoff`).
- `uploadDocument` clones the client before `->attach()` so multipart headers don't leak into later requests.

**3. Structs** (`src/Structs/Soa/v1/*`)
- Typed DTOs that **extend Eloquent `Model` but are never persisted**: the base `Struct::booted()` throws
  `StructSaveException` on any save attempt. They exist to reuse Eloquent casting, `$fillable`, `$casts`, attribute
  accessors, and factories.
- `Request` (base) exposes a `payload` accessor that strips `null` attributes and converts `BackedEnum` values to their
  scalar `->value` — this is the request body. Some endpoints send `->payload`, others send `->toArray()`; match the
  existing method when adding one.
- Response structs are constructed directly from the decoded JSON array
  (`new SignatureRequestResponse($response->json())`).
- `Struct::validate()` runs Laravel validation against per-struct `$rules`.

**4. Enums** (`src/Enums/v1/*`)
- Native PHP backed enums for fixed API vocabularies (delivery mode, signature level, document nature, etc.).
- The codebase migrated from class constants to native enums — prefer native enums for any new vocabulary.

**Factories** (`database/factories/v1/*`, all extend `AbstractFactory`) generate realistic Struct instances. They serve
double duty: fixtures in tests *and* the canned response bodies returned by `YousignFaker`. **Always create a Factory for
every new Struct** and wire it via `HasFactory` + `newFactory()`.

---

## Configuration

Published as `config/yousign-client.php` under key `yousign-client` (`YousignClientServiceProvider::CONFIG_KEY`).

### Environment Variables

```
YOUSIGN_URL=
YOUSIGN_API_KEY=
YOUSIGN_CUSTOM_EXPERIENCE_ID=
```

### Config Access

Always read config via typed `Config::` methods with the `CONFIG_KEY` prefix, as the lib does:

- ✅ `Config::string(YousignClientServiceProvider::CONFIG_KEY . '.url')`, `Config::integer(... . '.timeout_seconds')`
- ❌ `config('yousign-client.url')`

---

## Testing

- Built on **Pest** + **Orchestra Testbench** (the package is tested inside a booted Laravel kernel). The base
  `Tests\TestCase` registers `YousignClientServiceProvider` and sets the faker locale to `it_IT`. Test env
  (`YOUSIGN_URL`, `YOUSIGN_API_KEY`) is defined in `phpunit.xml`.
- The test tree mirrors `src/` one-to-one, using the **hierarchical TestCase** structure (each namespace has its own
  abstract `TestCase`; never `use Tests\TestCase;` in a test file).

### Two ways to fake outbound HTTP

**`Http::fake([...])` directly** — when asserting on the exact request (method, URL, headers, body):

```php
Http::fake(
    [
        $url => Http::response($expected->toArray(), Response::HTTP_CREATED),
    ]
);
```

**`Yousign::fake()`** — installs `YousignFaker`, which calls `Http::preventStrayRequests()`, registers default canned
responses (built from factories) for every endpoint, records the last called method, and exposes `assertIsCalled()`:

```php
Yousign::fake();

// ... code that calls Yousign::initiateSignature(...)

Yousign::assertIsCalled('initiateSignature', static fn (InitiateSignatureRequest $request): bool
    => $request->name === 'expected');
```

Override specific endpoints with `Yousign::fake([$url => Http::response(...)])`. `YousignFaker` throws unless
`app()->runningUnitTests()` — it must never reach production.

---

## Adding a New API Method

1. **Add the method to `src/Libs/Soa/v1/Yousign.php`** — build the URL from the `*_URL` constants and `URL_SEPARATOR`,
   send the Struct payload, guard the response shape, hydrate the response Struct.
2. **Create the request Struct** extending `Request` (snake_case `$fillable`, `$casts`, validation `$rules`).
3. **Create the response Struct** extending `Struct` with `@property` annotations for every field.
4. **Create a Factory** for each new Struct in `database/factories/v1/` and wire `HasFactory` + `newFactory()`.
5. **Add the `@method` annotation** to `src/Facades/v1/Yousign.php`.
6. **Register a fake response** in `YousignFaker` (map the endpoint to a factory-built `Http::response`).
7. **Write the test first** (TDD) under the mirrored `tests/Unit/...` path.

---

## Version Management

- Uses **semantic-release** (`.releaserc`), automated in GitHub Actions; on push to `master` it computes the version,
  generates `CHANGELOG.md`, tags, and syncs to Packagist.
- Conventional Commits are enforced by this tooling — a mis-typed commit silently produces the wrong release:
  - `feat!:` / `BREAKING CHANGE:` → major
  - `feat:` → minor
  - `fix:` → patch
- CI matrix tests Laravel `12.*` and `13.*` against both `prefer-lowest` and `prefer-stable` on PHP 8.3 — keep all four
  green.
