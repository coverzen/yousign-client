# Yousign Client

A Laravel package that wraps the [Yousign](https://yousign.com) e-signature **REST API (v3)** behind a typed,
testable client. It exposes one fluent facade, hydrates every response into typed data structures (Structs) and
ships a first-class fake so you can test the consuming code without hitting the network.

> This is a **client library** (`coverzen/yousign-client`), not a runnable application. It is installed as a
> Composer dependency inside a Laravel app.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [End-to-End Example](#end-to-end-example)
- [Method Reference](#method-reference)
- [Request Structs](#request-structs)
- [Enums](#enums)
- [Error Handling & Retries](#error-handling--retries)
- [Testing](#testing)
- [Versioning](#versioning)
- [Postman Collection](#postman-collection)

## Requirements

| Requirement | Version         |
|-------------|-----------------|
| PHP         | `^8.3`          |
| Laravel     | `12.x` / `13.x` |
| ext-json    | `*`             |

> **Heads up (v3.0.0):** the package now uses **native PHP enums** and drops `spatie/laravel-enum`, Laravel 11
> and PHP < 8.3. See [Versioning](#versioning) for migration notes.

## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require coverzen/yousign-client
```

The `YousignClientServiceProvider` is auto-discovered — no manual registration needed.

The facade is **not** aliased globally, so import it with its fully-qualified namespace:

```php
use Coverzen\Components\YousignClient\Facades\v1\Yousign;
```

## Configuration

Publish the config file (optional — `env` variables are enough to get started):

```bash
php artisan vendor:publish --provider="Coverzen\Components\YousignClient\YousignClientServiceProvider" --tag=config
```

This creates `config/yousign-client.php`.

### Environment Variables

```dotenv
YOUSIGN_URL=https://api-sandbox.yousign.app/v3
YOUSIGN_API_KEY=your-api-key
YOUSIGN_CUSTOM_EXPERIENCE_ID=
```

Use the sandbox base URL (`https://api-sandbox.yousign.app/v3`) for testing and the production URL
(`https://api.yousign.app/v3`) for live signatures.

### Config Reference

| Key                          | Env                            | Default | Description                                              |
|------------------------------|--------------------------------|---------|----------------------------------------------------------|
| `url`                        | `YOUSIGN_URL`                  | —       | Base URL of the Yousign API (sandbox or production).     |
| `api_key`                    | `YOUSIGN_API_KEY`              | —       | API key sent as `Authorization: Bearer <key>`.           |
| `connection_timeout_seconds` | —                              | `5`     | TCP connection timeout, in seconds.                      |
| `timeout_seconds`            | —                              | `5`     | Total request timeout, in seconds.                       |
| `retry_count`                | —                              | `1`     | Number of retries on connection failures.                |
| `retry_sleep`                | —                              | `10`    | Base backoff (ms) for the exponential retry strategy.    |
| `custom_experience_id`       | `YOUSIGN_CUSTOM_EXPERIENCE_ID` | —       | Optional Yousign custom experience, applied on initiate. |

## Quick Start

```php
use Coverzen\Components\YousignClient\Facades\v1\Yousign;
use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;

$signatureRequest = Yousign::initiateSignature(
    new InitiateSignatureRequest([
        'name' => 'Insurance contract',
        'delivery_mode' => DeliveryMode::email,
        'timezone' => 'Europe/Rome',
    ])
);

// $signatureRequest is a typed SignatureRequestResponse struct
$signatureRequestId = $signatureRequest->id;
```

## End-to-End Example

A complete signature flow: create the request, upload a document, add a signer, register their consent and
activate the request so Yousign sends out the invitation.

```php
use Coverzen\Components\YousignClient\Facades\v1\Yousign;
use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Coverzen\Components\YousignClient\Enums\v1\DocumentNature;
use Coverzen\Components\YousignClient\Enums\v1\SignatureAuthenticationMode;
use Coverzen\Components\YousignClient\Enums\v1\SignatureLevel;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;

// 1. Initiate the signature request
$signatureRequest = Yousign::initiateSignature(
    new InitiateSignatureRequest([
        'name' => 'Insurance contract',
        'delivery_mode' => DeliveryMode::email,
        'ordered_signers' => false,
        'timezone' => 'Europe/Rome',
    ])
);

// 2. Upload a signable document (raw binary or base64 — base64 is auto-decoded)
$document = Yousign::uploadDocument(
    $signatureRequest->id,
    new UploadDocumentRequest([
        'file_content' => file_get_contents('/path/to/contract.pdf'),
        'file_name' => 'contract.pdf',
        'nature' => DocumentNature::signable_document,
    ])
);

// 3. Add a signer, placing a signature field on the uploaded document
$signer = Yousign::addSigner(
    $signatureRequest->id,
    new AddSignerRequest([
        'info' => [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
            'phone_number' => '+39000000000',
            'locale' => 'it',
        ],
        'signature_level' => SignatureLevel::electronic_signature,
        'signature_authentication_mode' => SignatureAuthenticationMode::otp_email,
        'fields' => [
            new SignerField([
                'document_id' => $document->id,
                'type' => 'signature',
                'page' => 1,
                'x' => 100,
                'y' => 200,
                'width' => 150,
                'height' => 80,
            ]),
        ],
    ])
);

// 4. Register the signer's consent
Yousign::addConsent(
    $signatureRequest->id,
    new AddConsentRequest([
        'signer_ids' => [$signer->id],
        'optional' => false,
    ])
);

// 5. Activate the request — Yousign now notifies the signer
$activated = Yousign::activateSignature($signatureRequest->id);

// $activated->status is an ActivateSignatureResponseStatus enum (e.g. ::ongoing)
```

Once the request is active you can poll its state, download signed documents and audit trails:

```php
// Fetch the current state of the request
$current = Yousign::getSignatureById($signatureRequest->id);

// Download the signed document (returns raw PDF bytes)
$pdf = Yousign::getDocumentById($signatureRequest->id, $document->id);
file_put_contents('/path/to/signed-contract.pdf', $pdf);

// Download a signer's audit trail (returns raw PDF bytes)
$auditTrail = Yousign::getAuditTrail($signatureRequest->id, $signer->id);
```

## Method Reference

All methods are called statically through the `Yousign` facade. Request payloads are typed Structs; responses are
hydrated into typed Structs (or raw strings for binary downloads).

| Method                                                                | Yousign endpoint                                                       | Returns                       |
|-----------------------------------------------------------------------|------------------------------------------------------------------------|-------------------------------|
| `initiateSignature(InitiateSignatureRequest $request)`                | `POST signature_requests`                                              | `SignatureRequestResponse`    |
| `uploadDocument(string $id, UploadDocumentRequest $request)`          | `POST signature_requests/{id}/documents` (multipart)                   | `UploadDocumentResponse`      |
| `addSigner(string $id, AddSignerRequest $request)`                    | `POST signature_requests/{id}/signers`                                 | `AddSignerResponse`           |
| `addConsent(string $id, AddConsentRequest $request)`                  | `POST signature_requests/{id}/consent_requests`                        | `AddConsentResponse`          |
| `activateSignature(string $id)`                                       | `POST signature_requests/{id}/activate`                                | `ActivateSignatureResponse`   |
| `getSignatureById(string $id)`                                        | `GET signature_requests/{id}`                                          | `SignatureRequestResponse`    |
| `getConsentsById(string $id)`                                         | `GET signature_requests/{id}/consent_requests`                         | `GetConsentsResponse`         |
| `getDocumentById(string $id, string $documentId)`                     | `GET signature_requests/{id}/documents/{documentId}/download`          | `string` (raw PDF bytes)      |
| `getAuditTrail(string $id, string $signerId)`                         | `GET signature_requests/{id}/signers/{signerId}/audit_trails/download` | `string` (raw PDF bytes)      |
| `getAuditTrailDetail(string $id, string $signerId)`                   | `GET signature_requests/{id}/signers/{signerId}/audit_trails`          | `GetAuditTrailDetailResponse` |
| `cancelSignatureRequest(string $id, CancelSignatureRequest $request)` | `POST signature_requests/{id}/cancel`                                  | `SignatureRequestResponse`    |
| `deleteSignatureRequest(string $id)`                                  | `DELETE signature_requests/{id}`                                       | `void`                        |

## Request Structs

Request Structs are typed DTOs. Populate them via the constructor (`new InitiateSignatureRequest([...])`) or by
assigning properties. Enum fields accept native enum instances.

### `InitiateSignatureRequest`

| Field                | Type                        | Notes                                         |
|----------------------|-----------------------------|-----------------------------------------------|
| `name`               | `string\|null`              | Human-readable name of the signature request. |
| `delivery_mode`      | `DeliveryMode`              | Defaults to `DeliveryMode::none`.             |
| `ordered_signers`    | `bool`                      | Defaults to `false`.                          |
| `timezone`           | `string\|null`              | e.g. `Europe/Rome`.                           |
| `email_notification` | `array<string,mixed>\|null` | Optional email notification settings.         |

> `custom_experience_id` is injected automatically from config (`YOUSIGN_CUSTOM_EXPERIENCE_ID`) when set.

### `UploadDocumentRequest`

| Field          | Type             | Notes                                                              |
|----------------|------------------|--------------------------------------------------------------------|
| `file_content` | `string\|null`   | Raw binary **or** base64 — base64 content is detected and decoded. |
| `file_name`    | `string\|null`   | File name including extension (e.g. `contract.pdf`).               |
| `nature`       | `DocumentNature` | `signable_document` or `attachment`.                               |

### `AddSignerRequest`

| Field                           | Type                                | Notes                                                                             |
|---------------------------------|-------------------------------------|-----------------------------------------------------------------------------------|
| `info`                          | `array<string,mixed>`               | Signer details (`first_name`, `last_name`, `email`, `phone_number`, `locale`, …). |
| `signature_level`               | `SignatureLevel\|null`              | See [Enums](#enums).                                                              |
| `signature_authentication_mode` | `SignatureAuthenticationMode\|null` | See [Enums](#enums).                                                              |
| `fields`                        | `array<int,SignerField>\|null`      | Signature field placements (see below).                                           |

`SignerField` fields: `document_id`, `type`, `page`, `x`, `y`, `width`, `height`.

### `AddConsentRequest`

| Field        | Type                        | Notes                                  |
|--------------|-----------------------------|----------------------------------------|
| `type`       | `string`                    | Defaults to `checkbox`.                |
| `settings`   | `array<string,mixed>\|null` | Optional consent settings.             |
| `optional`   | `bool`                      | Defaults to `false`.                   |
| `signer_ids` | `array<int,string>`         | IDs of the signers this consent binds. |

### `CancelSignatureRequest`

| Field         | Type                    | Notes                                                            |
|---------------|-------------------------|------------------------------------------------------------------|
| `reason`      | `CancelSignatureReason` | Defaults to `CancelSignatureReason::contractualization_aborted`. |
| `custom_note` | `string\|null`          | Free-text note, required when `reason` is `other`.               |

## Enums

Native PHP backed enums under `Coverzen\Components\YousignClient\Enums\v1`. Use the case directly
(e.g. `DeliveryMode::email`); the client serialises it to its string value on the wire.

| Enum                              | Cases                                                                                                                                                                                 |
|-----------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `DeliveryMode`                    | `none`, `email`                                                                                                                                                                       |
| `DocumentNature`                  | `attachment`, `signable_document`                                                                                                                                                     |
| `SignatureLevel`                  | `electronic_signature`, `advanced_electronic_signature`, `electronic_signature_with_qualified_certificate`, `qualified_electronic_signature`, `qualified_electronic_signature_mode_1` |
| `SignatureAuthenticationMode`     | `otp_email`, `otp_sms`, `no_otp`                                                                                                                                                      |
| `CancelSignatureReason`           | `contractualization_aborted`, `errors_in_document`, `other`                                                                                                                           |
| `ActivateSignatureResponseStatus` | `draft`, `ongoing`, `done`, `expired`, `canceled`, `rejected`, `deleted`, `approval` (response-only)                                                                                  |

## Error Handling & Retries

- **HTTP errors** — the client calls `->throw()`, so any `4xx`/`5xx` response raises an
  `Illuminate\Http\Client\RequestException`. The failure is logged (`host`, `path`, status code, message)
  before the exception bubbles up.
- **Retries** — only `ConnectionException`s are retried (up to `retry_count`), using an exponential backoff:
  `2^(attempt-1) * retry_sleep`.
- **Malformed responses** — when Yousign returns a body that can't be hydrated into the expected shape, the
  client throws a `RuntimeException`.

```php
use Illuminate\Http\Client\RequestException;

try {
    $signatureRequest = Yousign::getSignatureById($id);
} catch (RequestException $e) {
    // Inspect $e->response, retry, surface to the user, …
}
```

## Testing

The package ships a fake that swaps the underlying client and prevents stray HTTP requests, so consuming code
can be tested without reaching Yousign.

### Faking the client

```php
use Coverzen\Components\YousignClient\Facades\v1\Yousign;

Yousign::fake();

// ... run the code under test that calls Yousign::initiateSignature(...) ...
```

`Yousign::fake()` registers default, factory-built responses for **every** endpoint, calls
`Http::preventStrayRequests()` and records the last called method.

### Asserting calls

```php
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;

Yousign::assertIsCalled(
    'initiateSignature',
    static fn (InitiateSignatureRequest $request): bool => $request->name === 'Insurance contract'
);
```

### Overriding specific responses

Pass an array of `url => Http::response(...)` overrides to `fake()` to control individual endpoints:

```php
use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

Yousign::fake([
    Config::string(YousignClientServiceProvider::CONFIG_KEY . '.url') . '/signature_requests' => Http::response(
        ['id' => 'fixed-id', 'status' => 'draft'],
        Response::HTTP_CREATED
    ),
]);
```

> `Yousign::fake()` throws unless it runs inside a test (`app()->runningUnitTests()`), so it can never leak into
> production.

### Asserting on the raw request

When you need to assert on the exact outbound request (method, URL, headers, body), fake the HTTP layer
directly with `Http::fake([...])` instead of the client fake.

## Versioning

The codebase is organised under a `v1/` namespace segment that tracks the **Yousign API version** — not the
package version. A future Yousign API would be added as a parallel `v2/` tree.

The package itself follows **semantic versioning** (automated via semantic-release / Conventional Commits).

### Migrating to v3.0.0

`v3.0.0` is a major release:

- Enums are now **native PHP enums** — use `Enum::case` instead of the old `Enum::case()` method call.
- `spatie/laravel-enum` has been removed.
- Enum-typed response fields (`status`, `delivery_mode`, `signature_level`,
  `signature_authentication_mode`) now return `BackedEnum` instances instead of plain strings.
- Laravel 11 and PHP < 8.3 are no longer supported (`illuminate/support: ^12.0 || ^13.0`, `php: ^8.3`).

## Postman Collection

You can find the official Postman collection
[here](https://app.getpostman.com/run-collection/14441723-8366a6fc-21b3-47df-b62c-4f14ac7f0907?action=collection%2Ffork&collection-url=entityId%3D14441723-8366a6fc-21b3-47df-b62c-4f14ac7f0907%26entityType%3Dcollection%26workspaceId%3D6822ec75-8a77-47dc-9065-5faae69db230).
