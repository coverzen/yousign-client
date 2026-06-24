# PHP + Laravel - AI Agent Guide

Quick reference for PHP and Laravel development standards.

---

## Quick Checklist

**Mandatory for all code:**

- [ ] `<?php declare(strict_types=1);` inline (strict types on same line as opening tag)
- [ ] All variables have PHPDoc type annotations with blank lines before/after
- [ ] All imports use `use` statements (never inline fully qualified names)
- [ ] All methods have PHPDoc with params, exceptions, return type
- [ ] Array types use `array<key,value>` syntax in PHPDoc
- [ ] Use `@inheritdoc` whenever possible to inherit documentation
- [ ] No magic numbers (use constants or enums)
- [ ] No N+1 queries (use eager loading or scopes)
- [ ] No useless variables (only create if used 2+ times)
- [ ] **Method chaining: second METHOD CALL ALWAYS on new line (properties stay on same line)**
- [ ] **Exception: if chained calls are inside another method call, keep them inline for readability**
- [ ] **Never specify default parameter values (e.g., `postJson($url)` not `postJson($url, [])`)**
- [ ] **Never use default values in parameters (e.g., `Http::response($data)` not `Http::response($data, 200)`)**
- [ ] **Use Symfony HTTP status constants instead of magic numbers (e.g., `Response::HTTP_CREATED` not `201`)**
- [ ] **If a variable is used only to call methods, DON'T create it - chain directly**
- [ ] **Always add PHPDoc annotations to foreach loop variables**
- [ ] **Use class constants for values that don't change within tests**
- [ ] **Use typed Config facade methods (Config::string(), Config::integer(), Config::boolean()) instead of config() helper**
- [ ] **Use PHPStan ignore comments (`@phpstan-ignore`) for false positives on validated data**
- [ ] **NEVER use variables to store static values - use class constants instead**
- [ ] **ALL variables must have PHPDoc type annotations - no exceptions**
- [ ] **ALWAYS use class constants for static data (arrays, strings, numbers) - NEVER variables**
- [ ] **Factory method calls ALWAYS multiline with proper indentation (e.g., `Tag::factory(3)\n->make();`)**
- [ ] **Follow existing code style: if codebase uses PHPDoc + multiline for factories, YOU MUST DO THE SAME**
- [ ] **Use `Model::factory(3)->make()` NOT `Model::factory()->count(3)->make()` for multiple instances**
- [ ] **NEVER create variables used only ONCE - inline them directly (e.g., `Http::response(Factory::make()->toArray())` not `$var = Factory::make(); Http::response($var->toArray())`)**
- [ ] **Variables used 2+ times MUST be created - don't repeat instantiation/method calls**

**Laravel Models:**

- [ ] Models have `@property` and `@property-read` annotations (all nullable by default)
- [ ] Models have `@mixin Builder<static>` annotation
- [ ] Use `@use HasFactory<ModelNameFactory>` when using HasFactory
- [ ] Don't define `$table` if following standard naming
- [ ] Don't define `$casts` for VARCHAR/TEXT or timestamps
- [ ] Only cast when needed: JSON, boolean, integer, float, date, array, collection, encrypted, custom casts, enums
- [ ] Common queries extracted to model scopes
- [ ] BelongsToMany relations use 4 template parameters: `<RelatedModel,$this,PivotModel,'pivot'>`
- [ ] **Eloquent attach()/detach()/sync() accept models directly, not just IDs**
- [ ] **Use dynamic properties for relations: `$model->relation` NOT `$model->relation()->get()`**

**Data Structures:**

- [ ] Complex data structures use Struct class
- [ ] Simple data structures use plain arrays
- [ ] Never mix patterns

**Testing:**

- [ ] PHPUnit/Pest with `#[Test]` and `#[CoversClass]` attributes
- [ ] Factories for model creation
- [ ] Tests mirror `src/` structure (this is a package, not an application)
- [ ] **Http::fake() responses: never specify default status codes (e.g., `Http::response($data)` not `Http::response($data, 200)`)**
- [ ] **Use Symfony Response constants for non-default status codes (e.g., `Response::HTTP_CREATED` for 201)**
- [ ] **Use hierarchical TestCase structure: each namespace has its own abstract TestCase**
- [ ] **Test classes extend local TestCase (no import needed - same namespace)**
- [ ] **Never import `use Tests\TestCase;` in test files**
- [ ] **Orchestra Testbench** - Base TestCase extends `Orchestra\Testbench\TestCase` for package testing
- [ ] **Faker locale**: `it_IT` (configured in base TestCase)
- [ ] Related assertions grouped without blank lines
- [ ] Actions separated from assertions with blank lines
- [ ] **Use route() helper with route names, never hardcoded URLs**
- [ ] **Add route existence tests for all controller routes**
- [ ] **Use constants from MigrationConstants for length validation tests**
- [ ] **Extract magic numbers to class constants (e.g., `EXPECTED_TAG_COUNT`)**
- [ ] **FormRequest tests are exhaustive and unit-focused (test all validation rules)**
- [ ] **Controller tests validate only response status codes for validation errors, not specific fields**
- [ ] **Avoid duplication: detailed validation testing belongs in FormRequest tests only**
- [ ] **FormRequest tests don't need RefreshDatabase trait (pure unit tests)**
- [ ] **Don't test authorize() method if not overridden (Laravel defaults to true)**
- [ ] **Use HTTP status names in test methods: `it_returns_not_found` not `it_returns_404`**
- [ ] **Use AssertableJson fluent methods for JSON assertions: `has()`, `missing()`, `where()`, `count()`, `etc()`**
- [ ] **NEVER manually inspect `->json()` array - use AssertableJson assertions instead**
- [ ] **Data providers ALWAYS defined immediately before the test method that uses them**
- [ ] **Use arrays of strings in loops instead of repeating assertions for similar checks**
- [ ] **NEVER use `$this->assertTrue(true)` - use `$this->expectNotToPerformAssertions()` instead**
- [ ] **When using `expectException()`, ALWAYS assert exception message with `expectExceptionMessage()`**
- [ ] **Avoid unnecessary variables in tests - inline when possible: `$this->assertInstanceOf(Struct::class, new Model())`**
- [ ] **Chain method calls directly on instantiation when variable not reused: `(new Request($data))->validate()`**

**Code Quality:**

- [ ] Run `ddev composer analyse` and `ddev composer php-cs-fixer-fix`
- [ ] Static analysis passes (no new baseline entries)
- [ ] PHPStan ignores go inside PHPDoc blocks (multiline), not inline comments
- [ ] No inline comments—code is self-documenting
- [ ] Multiline statements isolated with blank lines before/after
- [ ] **NEVER create unnecessary variables—if used once, don't declare it**
- [ ] **Use `$request->validated('field')` directly instead of extracting to variables**
- [ ] Never store model properties in variables—use `$model->property` directly
- [ ] **ALWAYS add PHPDoc to variable declarations—no exceptions**
- [ ] **Multiline method calls: each parameter on own line, parentheses alone**
- [ ] **Multiline array/closure formatting: maintain readability with proper indentation**
- [ ] **NEVER remove PHPDoc annotations (@var, @param, @return, etc.) - if they exist, they are needed**
- [ ] **PHPDoc must be present everywhere: all variables, parameters, return types, properties, constants**
- [ ] **When refactoring, preserve ALL existing PHPDoc annotations - do not remove them**

**Laravel Patterns:**

- [ ] Use `Config::string()`, `Config::integer()`, `Config::boolean()` not `config()` helper
- [ ] Use `EventClass::dispatch()`, not `event()` helper
- [ ] Use falsy conditions, not explicit `!== null` checks
- [ ] Use `$this->faker`, not `fake()` in factories
- [ ] Always use string interpolation `"{$var}"`, never concatenation `.`
- [ ] Prefer Laravel helpers (e.g., `Str::isUuid()`) over custom implementations
- [ ] Return models/collections directly when no custom status code needed
- [ ] **Use `static function` for route group closures in route files**

**Controllers & Validation:**

- [ ] Use FormRequest classes for validation, never inline in controllers
- [ ] Use Response constants instead of magic numbers (e.g., `Response::HTTP_CREATED`)
- [ ] Return Eloquent models/collections directly when default 200 status is fine
- [ ] Use Laravel assertion methods (e.g., `assertCreated()` not `assertStatus(201)`)
- [ ] Test FormRequest validation rules unitarily
- [ ] **Use route model binding: controller methods receive Model instances, not IDs**

**Routes:**

- [ ] Use `apiResource()` for RESTful API endpoints
- [ ] Use `->except([])` to exclude specific routes (e.g., `->except(['update'])`)
- [ ] Use `static function` for route group closures
- [ ] Multiline route definitions for better readability
- [ ] Always test route existence in controller tests
- [ ] **Use route model binding with typed parameters (e.g., `{document}` resolves to `Document` model)**

**DDEV Development:**

- [ ] All commands via DDEV: `ddev composer`, `ddev artisan`, `ddev php`
- [ ] Never run PHP commands in local shell

---

## DDEV Environment

All PHP commands must run in DDEV containers. Never execute locally.

**Startup sequence:**

```bash
ddev start
ddev composer install
ddev artisan migrate
```

**Command examples:**

- ✅ `ddev composer test`, `ddev artisan migrate`, `ddev php artisan tinker`
- ❌ `composer test`, `artisan migrate`, `php artisan tinker`

---

## Code Standards

### Strict Types

Every PHP file starts with: `<?php declare(strict_types=1);`

### Comments Philosophy

Write self-documenting code:

- Code explains *what* through naming and structure
- Comments only explain *why* when not obvious
- No comments that restate what code does
- PHPDoc for types only—no useless descriptions

### Imports

- Always import classes with `use` statements
- Never inline fully qualified names (`\App\Models\User`)
- Import classes even for PHPDoc type hints

---

## Type Annotations

### PHPDoc Requirements

- All variables must have PHPDoc type annotations
- All methods must have PHPDoc with `@param`, `@throws`, `@return`
- Use `array<key,value>` syntax, not `Type[]`
- Use `@inheritdoc` whenever possible

### Variable PHPDoc Spacing

Variables with PHPDoc must be isolated with blank lines before and after.

**Exception:** Variables immediately returned don't need extraction—return directly.

### PHPStan Ignore Annotations

When PHPDoc exists, add `@phpstan-ignore` inside the PHPDoc block (convert to multiline). Never use inline `//` comments
for ignores when PHPDoc exists.

### Method PHPDoc

Document all parameters, exceptions, and return types:

- `@param` for each parameter with type and description if needed
- `@throws` for exceptions
- `@return` for return type
- Use `@inheritdoc` to inherit from parent/interface

### Array Types

Always use generic syntax:

- ✅ `array<int,string>`, `array<string,mixed>`, `Collection<int,Model>`
- ❌ `string[]`, `Model[]`

---

## Static Analysis & Quality Tools

Run after any code changes:

```bash
ddev composer php-cs-fixer-fix # Auto-fix code style
ddev composer analyse          # All quality checks
```

**Tools in `composer analyse`:**

- PHP-CS-Fixer: Code style
- PHPStan: Static analysis
- Psalm: Additional type checking
- PHP CodeSniffer: Coding standards
- PHP Mess Detector: Quality issues
- PHP Magic Number Detector: Magic numbers
- PHP Insights: Overall code quality

**All tools must pass** before committing.

---

## Laravel Models

### Essential Annotations

All models require:

- `@property` for database columns (nullable by default unless `$attributes` default exists)
- `@property-read` for accessors, computed properties, and relations
- `@mixin Builder<static>` for query builder support

### HasFactory Trait

Place factory generic annotation immediately above trait use:

```php
/** @use HasFactory<UserFactory> */
use HasFactory;
```

### Property Nullability

All `@property` must include `null` in type union, except properties with defaults in `$attributes`. New model instances
have all properties `null` until populated from database or set explicitly.

### Model Casts

Only specify `$casts` when necessary. Laravel auto-casts:

- VARCHAR/TEXT → `string`
- Timestamps → `Carbon`

Cast only when needed: JSON, boolean, integer, float, date, array, collection, encrypted fields, custom casts, enums.

### Table Names

Don't define `$table` if following Laravel naming conventions (pluralized snake_case of model name).

### Model Scopes

Extract common queries to scopes for reusability:

```php
public function scopeActive(Builder $query): Builder
{
    return $query->where('status', 'active');
}
```

### BelongsToMany Relations

Document with 4 template parameters:

1. Related Model
2. Declaring Model (use `$this`, never `static` or class name)
3. Pivot Model (`WithTimestampsPivot`, `Pivot`, etc.)
4. Pivot Accessor (always `'pivot'`)

**Class PHPDoc:**

- `@property-read Collection<int,RelatedModel> $relationName`
- `@property-read PivotModel|null $pivot`

**Method PHPDoc:**

- `@return BelongsToMany<RelatedModel,$this,PivotModel,'pivot'>`

Import all classes. Use `->using()` for custom pivot, `->withTimestamps()` if pivot has timestamps.

---

## Data Structures

### When to Use Struct

Use Struct class for:

- Complex data structures with multiple fields
- Objects passed between services
- Data needing validation or methods

### When to Use Arrays

Use arrays for:

- Simple key-value pairs
- Temporary data
- API payloads
- Short-lived data

**Never mix Struct and array patterns for the same concept.**

---

## Code Formatting

### Multiline Statement Spacing (PRIORITY RULE)

Any statement spanning multiple lines MUST be isolated with blank lines before and after. This includes:

- Multiline method calls
- Multiline function calls
- Multiline array definitions
- Multiline conditionals
- Multiline assignments

### Method Call Formatting

**Single line:** Keep on one line if all parameters fit

**Multiline (STRICT RULE):** If ANY parameter is multiline (e.g., array), then:
1. Opening parenthesis stays on same line as method name
2. Each parameter on its own line
3. Closing parenthesis on its own line
4. Apply to ALL parameters, even simple ones

**Bad:**
```php
$response = $this->postJson('/api/v1/tags', [
    'key' => 'category',
    'value' => 'invoice',
]);
```

**Good:**
```php
$response = $this->postJson(
    '/api/v1/tags',
    [
        'key' => 'category',
        'value' => 'invoice',
    ]
);
```

**Chained methods:** From 2nd method onward, break to new line aligned under first method:
```php
$users = User::where('active', true)
             ->orderBy('created_at', 'desc')
             ->limit(10)
             ->get();
```

### Variable PHPDoc (MANDATORY)

**ALWAYS add PHPDoc for variable declarations.** No exceptions.

```php
// ✅ Good: PHPDoc always present
/** @var User $user */
$user = User::find($id);

/** @var TestResponse $response */
$response = $this->postJson(
    '/api/v1/tags',
    [
        'key' => 'test',
        'value' => 'value',
    ]
);

// ❌ Bad: Missing PHPDoc
$user = User::find($id);
$response = $this->postJson('/api/v1/tags', $data);
```

### Blank Lines Rules

- Blank line after multiline statements
- Blank line before/after PHPDoc + variable blocks
- Related assertions grouped without blank lines
- Blank line separating actions from assertions
- No blank lines at start/end of methods or control structures

---

## Database Queries

### N+1 Query Prevention

Always use eager loading or scopes. Never query in loops.

**Eager loading:**

```php
$documents = Document::with('tags')->get();
```

**Scopes for complex queries:**

```php
$active = User::active()->recent()->get();
```

### Migration Best Practices

- Omit string length if 255 (Laravel default)
- Use constants from `database/constants/MigrationConstants.php` for non-default lengths
- Never use magic numbers

---

## Testing

### Test Structure

- Use **Pest PHP** (this project) with `#[Test]` and `#[CoversClass]` attributes
- Mirror `src/` directory structure in `tests/Unit/` (this is a package, not an application)
- Test files suffixed with `Test.php`
- Use factories for model creation, never manual instantiation
- **Factory syntax for multiple instances: use `Model::factory(3)->make()` NOT `Model::factory()->count(3)->make()`**
- **Always use route() helper with named routes, never hardcoded URLs**
- **Add route existence tests for all controller endpoints**
- **Use constants from MigrationConstants, never magic numbers**

### Orchestra Testbench (Package Testing)

This is a **Laravel package**, not an application. Testing uses Orchestra Testbench:

- Base `TestCase` extends `Orchestra\Testbench\TestCase`
- Override `getPackageProviders()` to register package service provider
- Configure package-specific settings in `setUp()` method
- No database by default (this is a client package)
- **Faker locale**: `it_IT` (configured in base TestCase via `Config::set('app.faker_locale', 'it_IT')`)

**Running tests:**
```bash
ddev composer test                      # All tests
ddev composer test -- --filter=method   # Single test by method name
```

### TestCase Hierarchy

**IMPORTANT: Use hierarchical TestCase structure for better test organization.**

Every test namespace has its own abstract `TestCase` class that extends the parent namespace's `TestCase`:

```
tests/
├── TestCase.php (extends Laravel's BaseTestCase)
├── Unit/
│   ├── TestCase.php (extends Tests\TestCase)
│   ├── Actions/
│   │   ├── TestCase.php (extends Tests\Unit\TestCase)
│   │   └── v1/
│   │       └── TestCase.php (extends Tests\Unit\Actions\TestCase)
│   ├── Http/
│   │   ├── TestCase.php (extends Tests\Unit\TestCase)
│   │   └── ...
│   └── Models/
│       ├── TestCase.php (extends Tests\Unit\TestCase)
│       └── ...
└── Feature/
    └── TestCase.php (extends Tests\TestCase)
```

**Rules:**
- Each namespace directory contains an abstract `TestCase` class
- Test classes extend their local `TestCase` (no import needed - same namespace)
- Never import `use Tests\TestCase;` in test files
- Each TestCase can contain shared logic for tests at that level
- Abstract TestCases inherit from parent level, creating a clean hierarchy

**Example:**
```php
<?php declare(strict_types=1);

namespace Tests\Unit\Actions\v1;

// No TestCase import needed - using local one
final class DeleteDocumentFileTest extends TestCase
{
    // Test methods...
}
```

**Benefits:**
- Clean organization: shared logic at appropriate levels
- No imports needed: TestCase resolved automatically from same namespace
- Extensible: easy to add helpers at any level
- Maintainable: clear hierarchy and inheritance chain

### Route Testing

Test that routes exist and resolve correctly:

```php
#[Test]
public function it_has_show_route(): void
{
    $tag = Tag::factory()->create();
    
    $this->assertNotEmpty(route('tags.show', $tag));
    $this->assertStringContainsString((string) $tag->id, route('tags.show', $tag));
}
```

### Test Formatting

- Arrange, Act, Assert sections separated by blank lines
- Related assertions grouped without blank lines
- Actions separated from assertions with blank line
- **NEVER create variables if used only once—pass inline directly**
- **When creating models/objects for immediate use, chain factory inline:**
  ```php
  // ✅ Good: Factory used inline
  (new DeleteTemporaryFile())->handle(
      new DocumentCreated(
          Document::factory()
                  ->make(['storage_path' => self::STORAGE_PATH])
      )
  );

  // ❌ Bad: Unnecessary variable
  /** @var Document $document */
  $document = Document::factory()
                      ->make(['storage_path' => self::STORAGE_PATH]);

  (new DeleteTemporaryFile())->handle(new DocumentCreated($document));
  ```
- Never store model properties in variables
- Use Laravel assertion methods

**Bad - Hardcoded URL:**
```php
$this->postJson('/api/v1/tags', $data);
```

**Good - Named route:**
```php
$this->postJson(route('tags.store'), $data);
```

**Bad - Magic number:**
```php
'key' => str_repeat('a', 101)
```

**Good - Constant + 1:**
```php
'key' => str_repeat('a', MigrationConstants::TAG_KEY_MAX_LENGTH + 1)
```

---

## String Interpolation

Always use string interpolation with curly braces, never concatenation:

✅ `"{$var}"`, `"path/{$id}/file"`
❌ `$var`, `'path/' . $id . '/file'`

**Rules:**

1. Always use curly braces: `"{$var}"` not `"$var"`
2. Extract complex expressions to variables first
3. Single quotes for static strings: `'text'`
4. Double quotes for interpolation: `"text with {$variable}"`

---

## Laravel Patterns

### Config Access

Use typed `Config::` facade methods, never the `config()` helper:

- ✅ `Config::string('app.name')`, `Config::integer('cache.ttl')`, `Config::boolean('app.debug')`
- ❌ `config('app.name')`, `config('cache.ttl')`, `config('app.debug')`

**Available typed methods:**
- `Config::string()` - for string values
- `Config::integer()` - for integer values  
- `Config::boolean()` - for boolean values
- `Config::array()` - for array values
- `Config::float()` - for float values

### Event Dispatching

Use class-based dispatching:

- ✅ `DocumentCreated::dispatch($document)`
- ❌ `event(new DocumentCreated($document))`

### Event-Listener Pattern with TDD

**Use model's `$dispatchesEvents` property for declarative event binding:**

```php
/** @var array<string,class-string> */
protected $dispatchesEvents = [
    'forceDeleted' => DocumentForceDeleted::class,
];
```

**Important Eloquent events:**
- `deleted` - triggers on both soft delete and force delete
- `forceDeleted` - triggers ONLY on force delete (use when you need to distinguish)
- `created`, `updated`, `saved`, `restored` - other common events

**TDD Approach for implementing event-listener pattern:**

1. **Create the event class** (simple data carrier):
```php
final class DocumentForceDeleted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Document $document
    ) {
    }
}
```

2. **Write listener tests FIRST** (RED phase):
```php
final class LogDocumentDeletionTest extends TestCase
{
    /** @var array<string,mixed> */
    private const array KNOWN_DOCUMENT_DATA = [
        'id' => 'test-uuid-123',
        'filename' => 'test.pdf',
        'storage_path' => 's3://test/path.pdf',
    ];

    #[Test]
    public function it_listens_to_document_deleted_event(): void
    {
        Event::fake();
        Event::assertListening(DocumentForceDeleted::class, LogDocumentDeletion::class);
    }

    #[Test]
    public function it_logs_document_deletion_with_all_details(): void
    {
        Log::expects('warning')
           ->once()
           ->with('Document force deleted', self::KNOWN_DOCUMENT_DATA);

        (new LogDocumentDeletion())->handle(
            new DocumentForceDeleted(Document::factory()->make(self::KNOWN_DOCUMENT_DATA))
        );
    }
}
```

3. **Implement the listener** (GREEN phase):
```php
final class LogDocumentDeletion
{
    public function handle(DocumentForceDeleted $event): void
    {
        Log::warning(
            'Document force deleted',
            [
                'id' => $event->document->id,
                'filename' => $event->document->filename,
                'storage_path' => $event->document->storage_path,
            ]
        );
    }
}
```

4. **Add model event dispatching**:
```php
/** @var array<string,class-string> */
protected $dispatchesEvents = [
    'forceDeleted' => DocumentForceDeleted::class,
];
```

5. **Test model event dispatching**:
```php
#[Test]
public function it_dispatches_document_force_deleted_event_when_force_deleting(): void
{
    Event::fake([DocumentForceDeleted::class]);

    Document::factory()
            ->create()
            ->forceDelete();

    Event::assertDispatched(DocumentForceDeleted::class);
}

#[Test]
public function it_does_not_dispatch_document_force_deleted_event_when_soft_deleting(): void
{
    Event::fake([DocumentForceDeleted::class]);

    Document::factory()
            ->create()
            ->delete();

    Event::assertNotDispatched(DocumentForceDeleted::class);
}
```

**Testing best practices:**
- Use class constants for static test data (avoid inline arrays)
- Focus on macro-functionality, not edge cases
- Test listener auto-discovery with `Event::assertListening()`
- Use `Event::fake()` to test event dispatching without executing listeners
- Use `Event::assertDispatched()` / `Event::assertNotDispatched()` to verify behavior
- Mock facades (like `Log`) to verify listener behavior without side effects
- Laravel auto-discovers listeners via type hints (no manual registration needed)

**Benefits:**
- Separation of concerns (model dispatches, listener handles side effects)
- Testable in isolation
- Easy to add more listeners without modifying existing code
- Controllers stay clean and focused on HTTP concerns

### Null Checking

Use falsy conditions:

- ✅ `if ($user)`, `if (!$user)`
- ❌ `if ($user !== null)`, `if ($user === null)`

### Factory Faker

In factories, use `$this->faker`:

- ✅ `$this->faker->word()`
- ❌ `fake()->word()`

**Note**: This project uses `it_IT` locale for faker (Italian), configured in base TestCase.

### Laravel Utilities

Prefer built-in helpers:
- ✅ `Str::isUuid($value)`, `Str::slug($text)`
- ❌ Custom regex for UUIDs, custom slug generation

### Controllers & Validation

**Always use FormRequest classes:**
- Encapsulate validation logic in FormRequest classes
- Never use inline `$request->validate()` in controllers
- Test FormRequest rules unitarily in `tests/Unit/Http/Requests/`

**Use Response constants:**
- ✅ `Response::HTTP_CREATED`, `Response::HTTP_NO_CONTENT`, `Response::HTTP_NOT_FOUND`
- ❌ Magic numbers: `201`, `204`, `404`

**Use route model binding:**
- Controller methods should receive Model instances, not string IDs
- Laravel automatically handles 404 responses when model not found
- Define routes with model parameter names: `Route::get('documents/{document}', ...)`
- Type-hint controller parameters with the model class

```php
// ✅ Good: Route model binding
Route::get('documents/{document}', [DocumentController::class, 'show']);

public function show(Document $document): Document
{
    return $document;
}

public function update(UpdateDocumentRequest $request, Document $document): Document
{
    $document->update($request->validated());
    return $document;
}

// ✅ Good: Multiple models
Route::delete('documents/{document}/tags/{tag}', [DocumentTagController::class, 'destroy']);

public function destroy(Document $document, Tag $tag): Response
{
    $document->tags()->detach($tag);
    return response()->noContent();
}

// ❌ Bad: Manual findOrFail
public function show(string $id): Document
{
    return Document::findOrFail($id);
}

public function update(UpdateDocumentRequest $request, string $id): Document
{
    $document = Document::findOrFail($id);
    $document->update($request->validated());
    return $document;
}
```

**Return Eloquent models directly when possible:**
- If no custom status code needed, return model/collection directly
- Laravel automatically converts to JSON with 200 status
- Only use `response()->json()` when custom status code required

```php
// ✅ Good: Return model directly (200 OK)
public function show(Document $document): Document
{
    return $document;
}

// ✅ Good: Return collection directly (200 OK)
/**
 * @return Collection<int,Tag>
 */
public function index(): Collection
{
    return Tag::all();
}

// ✅ Good: Custom status code (201 Created)
public function store(StoreTagRequest $request): JsonResponse
{
    return response()->json(
        Tag::create($request->validated()),
        Response::HTTP_CREATED
    );
}

// ❌ Bad: Unnecessary response()->json() wrapper
public function show(Document $document): JsonResponse
{
    return response()->json($document);
}
```

### Testing Assertions

Use Laravel assertion methods instead of generic status codes:
- ✅ `assertCreated()`, `assertNoContent()`, `assertNotFound()`, `assertOk()`, `assertUnprocessable()`
- ❌ `assertStatus(201)`, `assertStatus(204)`, `assertStatus(404)`, `assertStatus(200)`, `assertStatus(422)`

---

## Modern PHP Features

### PHP 8.0+

Named arguments, union types, constructor promotion, match expressions, nullsafe operator `?.`

### PHP 8.1+

**Enums** (use instead of constants), readonly properties, first-class callables, never return type

### PHP 8.2+

Readonly classes, disjunctive normal form types

### PHP 8.3+

Typed class constants, override attribute

---

## Questions When Uncertain

1. Should this be an Action, Service, or stay in Controller?
2. Does this need to be queued or synchronous?
3. Which database connection for this operation?
4. Is there an existing pattern to follow?
5. Should I add to baseline or fix properly?
6. Does this need API versioning (v1/v2)?

---

**Write code that is clear, well-typed, and self-documenting. Let the code speak for itself.**
